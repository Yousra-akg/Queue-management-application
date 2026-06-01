<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.soliqueue.base_url');
    }

    protected function get(string $endpoint, array $query = [])
    {
        $url = "{$this->baseUrl}/{$endpoint}";
        Log::info("API GET Request: {$url} with query " . json_encode($query));
        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->connectTimeout(3)
                ->get($url, $query);
            Log::info("API GET Response [{$response->status()}]: " . substr($response->body(), 0, 500));
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error("API GET Exception: " . $e->getMessage());
            throw $e;
        }
    }

    protected function post(string $endpoint, array $data = [])
    {
        $url = "{$this->baseUrl}/{$endpoint}";
        Log::info("API POST Request: {$url} with data " . json_encode($data));
        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->connectTimeout(3)
                ->post($url, $data);
            Log::info("API POST Response [{$response->status()}]: " . substr($response->body(), 0, 500));
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error("API POST Exception: " . $e->getMessage());
            throw $e;
        }
    }

    protected function handleResponse($response)
    {
        if ($response->successful()) {
            return $response->json();
        }

        $errorMsg = $response->json('message') ?? 'Une erreur est survenue lors de l\'appel API.';
        Log::warning("API Error Handled: {$errorMsg}");
        throw new \Exception($errorMsg);
    }
}

