<?php

namespace Lantera\ExtensionFramework\Services\Bigcommerce;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    public function __construct(
        private readonly string $storeHash,
        private readonly string $accessToken,
        private readonly string $version = 'v3',
    ) {}

    public function v2(): self
    {
        return new self($this->storeHash, $this->accessToken, 'v2');
    }

    public function v3(): self
    {
        return new self($this->storeHash, $this->accessToken, 'v3');
    }

    public function get(string $endpoint, array $query = []): Response
    {
        return $this->client()->get($endpoint, $query);
    }

    public function post(string $endpoint, array $data = []): Response
    {
        return $this->client()->post($endpoint, $data);
    }

    public function put(string $endpoint, array $data = []): Response
    {
        return $this->client()->put($endpoint, $data);
    }

    public function patch(string $endpoint, array $data = []): Response
    {
        return $this->client()->patch($endpoint, $data);
    }

    public function delete(string $endpoint): Response
    {
        return $this->client()->delete($endpoint);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl("https://api.bigcommerce.com/stores/{$this->storeHash}/{$this->version}/")
            ->withHeaders([
                'X-Auth-Token' => $this->accessToken,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }
}
