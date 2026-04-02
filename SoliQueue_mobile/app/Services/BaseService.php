<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

abstract class BaseService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.soliqueue.base_url');
    }

    protected function get(string $endpoint, array $query = [])
    {
        $response = Http::get("{$this->baseUrl}/{$endpoint}", $query);
        return $this->handleResponse($response);
    }

    protected function post(string $endpoint, array $data = [])
    {
        $response = Http::post("{$this->baseUrl}/{$endpoint}", $data);
        return $this->handleResponse($response);
    }

    protected function handleResponse($response)
    {
        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception($response->json('message') ?? 'Une erreur est survenue lors de l\'appel API.');
    }
}
