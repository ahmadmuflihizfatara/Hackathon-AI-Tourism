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
    public function handle(\App\Services\LmStudioService $lmService)
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
            
            $this->info("Found {$total} records. Ingesting...");
            
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($rows as $row) {
                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    $bar->advance();
                    continue;
                }

                // Construct text representation
                $contentParts = [];
                foreach ($headers as $index => $header) {
                    $value = $row[$index] ?? '';
                    if (!empty($value)) {
                        $contentParts[] = "{$header}: {$value}";
                    }
                }
                
                $content = implode(" | ", $contentParts);
                
                // Get embedding
                $embedding = $lmService->embed($content);
                
                if ($embedding) {
                    \App\Models\KnowledgeBase::create([
                        'content' => $content,
                        'embedding' => $embedding
                    ]);
                } else {
                    $this->error("\nFailed to get embedding for: " . substr($content, 0, 50) . "...");
                }

                $bar->advance();
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
