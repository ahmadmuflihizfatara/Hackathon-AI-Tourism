<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rag:ingest {file : The path to the XLSX dataset file}')]
#[Description('Ingest an XLSX dataset to the knowledge base with embeddings')]
class IngestDatasetCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(\App\Services\GeminiApiService $lmService, \App\Services\ChromaDbService $chromaService)
    {
        ini_set('memory_limit', '-1'); // Mencegah error exhausted memory

        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Reading dataset: {$file}");

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            if (empty($rows)) {
                $this->warn("Dataset is empty.");
                return 0;
            }

            $headers = array_shift($rows);
            $total = count($rows);
            
            $this->info("Found {$total} records. Ingesting to ChromaDB...");
            
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $batchSize = 50;
            $batchIds = [];
            $batchEmbeddings = [];
            $batchDocuments = [];
            $batchMetadatas = [];

            foreach ($rows as $index => $row) {
                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    $bar->advance();
                    continue;
                }

                // Construct text representation
                $contentParts = [];
                foreach ($headers as $colIndex => $header) {
                    $value = $row[$colIndex] ?? '';
                    if (!empty($value)) {
                        $contentParts[] = "{$header}: {$value}";
                    }
                }
                
                $content = implode(" | ", $contentParts);
                
                // Get embedding
                $embedding = $lmService->embed($content);
                
                if ($embedding) {
                    $id = uniqid("doc_{$index}_", true);
                    $batchIds[] = $id;
                    $batchEmbeddings[] = $embedding;
                    $batchDocuments[] = $content;
                    $batchMetadatas[] = ['source' => 'dataset_xlsx', 'row_index' => $index];

                    if (count($batchIds) >= $batchSize) {
                        $chromaService->addDocuments($batchIds, $batchEmbeddings, $batchDocuments, $batchMetadatas);
                        $batchIds = [];
                        $batchEmbeddings = [];
                        $batchDocuments = [];
                        $batchMetadatas = [];
                    }
                } else {
                    $this->error("\nFailed to get embedding for: " . substr($content, 0, 50) . "...");
                }

                $bar->advance();
            }

            // Insert remaining
            if (count($batchIds) > 0) {
                $chromaService->addDocuments($batchIds, $batchEmbeddings, $batchDocuments, $batchMetadatas);
            }

            $bar->finish();
            $this->newLine();
            $this->info("Ingestion completed successfully!");

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
