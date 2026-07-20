<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RagService
{
    public function __construct(
        protected GeminiApiService $llmService,
        protected ChromaDbService $chromaService
    ) {
    }

    /**
     * Search ChromaDB for the most relevant context based on a query.
     */
    public function searchContext(string $query, int $topK = 5): string
    {
        $queryEmbedding = $this->llmService->embed($query);

        if (!$queryEmbedding) {
            Log::warning('RAG: Failed to generate embedding for query', ['query' => $query]);
            return '';
        }

        $results = $this->chromaService->queryDocuments($queryEmbedding, $topK);

        if (empty($results['documents'])) {
            return '';
        }

        $documents = $results['documents'];
        $distances = $results['distances'];

        // Concatenate context
        $contextString = "";
        foreach ($documents as $index => $content) {
            // Distances in Chroma depend on distance function used (e.g. L2, cosine). 
            // We just output the raw distance for reference.
            $distance = number_format($distances[$index] ?? 0, 4);
            $contextString .= "[Context " . ($index + 1) . " (Distance: {$distance})]:\n" . $content . "\n\n";
        }

        return trim($contextString);
    }
}
