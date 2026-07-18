<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Log;

class RagService
{
    public function __construct(protected LmStudioService $llmService)
    {
    }

    /**
     * Search the KnowledgeBase for the most relevant context based on a query.
     */
    public function searchContext(string $query, int $topK = 5): string
    {
        $queryEmbedding = $this->llmService->embed($query);

        if (!$queryEmbedding) {
            Log::warning('RAG: Failed to generate embedding for query', ['query' => $query]);
            return '';
        }

        $documents = KnowledgeBase::all();
        $results = [];

        foreach ($documents as $doc) {
            $docEmbedding = $doc->embedding;
            if (is_array($docEmbedding) && count($docEmbedding) > 0) {
                $similarity = $this->cosineSimilarity($queryEmbedding, $docEmbedding);
                $results[] = [
                    'content' => $doc->content,
                    'similarity' => $similarity
                ];
            }
        }

        if (empty($results)) {
            return '';
        }

        // Sort descending by similarity
        usort($results, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Get top K
        $topResults = array_slice($results, 0, $topK);

        // Concatenate context
        $contextString = "";
        foreach ($topResults as $index => $result) {
            $score = number_format($result['similarity'], 4);
            $contextString .= "[Context " . ($index + 1) . " (Score: {$score})]:\n" . $result['content'] . "\n\n";
        }

        return trim($contextString);
    }

    /**
     * Calculate cosine similarity between two vectors.
     */
    protected function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        $count = min(count($vec1), count($vec2));

        for ($i = 0; $i < $count; $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $normA += pow($vec1[$i], 2);
            $normB += pow($vec2[$i], 2);
        }

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
