<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChromaDbService
{
    protected string $apiUrl;
    protected string $collectionName;
    protected ?string $collectionId = null;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.chromadb.url', 'http://localhost:8000/api/v1'), '/');
        $this->collectionName = config('services.chromadb.collection', 'tourism_knowledge_base');
    }

    /**
     * Get or create the configured ChromaDB collection and return its ID.
     */
    public function getCollectionId(): ?string
    {
        if ($this->collectionId) {
            return $this->collectionId;
        }

        try {
            $response = Http::timeout(30)->post("{$this->apiUrl}/collections", [
                'name' => $this->collectionName,
                'get_or_create' => true,
            ]);

            if ($response->successful()) {
                $this->collectionId = $response->json('id');
                return $this->collectionId;
            }

            Log::error('ChromaDB: Failed to get or create collection', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('ChromaDB Exception in getCollectionId', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Add documents and their embeddings to the collection.
     * 
     * @param array $ids Array of unique string IDs for the documents
     * @param array $embeddings Array of embeddings (each embedding is an array of floats)
     * @param array $documents Array of text documents
     * @param array $metadatas Array of metadata associative arrays
     */
    public function addDocuments(array $ids, array $embeddings, array $documents, array $metadatas = []): bool
    {
        $collectionId = $this->getCollectionId();
        if (!$collectionId) {
            return false;
        }

        $payload = [
            'ids' => $ids,
            'embeddings' => $embeddings,
            'documents' => $documents,
        ];

        if (!empty($metadatas)) {
            $payload['metadatas'] = $metadatas;
        }

        try {
            $response = Http::timeout(60)->post("{$this->apiUrl}/collections/{$collectionId}/add", $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('ChromaDB: Failed to add documents', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('ChromaDB Exception in addDocuments', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Query the collection using an embedding.
     * 
     * @param array $queryEmbedding The embedding vector to query with
     * @param int $nResults Number of results to return
     * @return array Returned format: ['documents' => [...], 'metadatas' => [...], 'distances' => [...]]
     */
    public function queryDocuments(array $queryEmbedding, int $nResults = 5): array
    {
        $collectionId = $this->getCollectionId();
        if (!$collectionId) {
            return [];
        }

        try {
            $response = Http::timeout(60)->post("{$this->apiUrl}/collections/{$collectionId}/query", [
                'query_embeddings' => [$queryEmbedding],
                'n_results' => $nResults,
                'include' => ['documents', 'metadatas', 'distances']
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Chroma returns an array of results for each query.
                // Since we sent 1 query, we take the first item (index 0) from the results.
                return [
                    'documents' => $data['documents'][0] ?? [],
                    'metadatas' => $data['metadatas'][0] ?? [],
                    'distances' => $data['distances'][0] ?? [],
                ];
            }

            Log::error('ChromaDB: Failed to query documents', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error('ChromaDB Exception in queryDocuments', ['error' => $e->getMessage()]);
        }

        return [];
    }
}
