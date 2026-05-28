<?php

namespace Lantera\ExtensionFramework\Services\AppsManager;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lantera\ExtensionFramework\Models\Site;

readonly class ApiClient
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?string $applicationSecretKey = null,
        private ?Site   $site = null
    )
    {
    }

    /**
     * @throws ConnectionException
     */
    private function handleResponse(Response $response): Response
    {
        $error = match ($response->status()) {
            401 => 'Unauthorized: Please check your application secret key or credentials.',
            403 => 'Forbidden: You do not have permission to access this resource.',
            422 => 'Unprocessable Entity: Validation failed. ' . json_encode($response->json('errors')),
            default => null,
        };

        if ($error === null) {
            return $response;
        }

        if (app()->isProduction()) {
            Log::error('[AppsManager] ' . $error, [
                'status' => $response->status(),
                'endpoint' => $response->transferStats?->getEffectiveUri()?->getPath(),
            ]);

            return $response;
        }

        throw new ConnectionException($error);
    }

    /**
     * @throws ConnectionException
     */
    public function GET(string $endpoint, array $query = []): Response
    {
        return $this->handleResponse($this->client()->get($endpoint, $query));
    }

    /**
     * @throws ConnectionException
     */
    public function POST(string $endpoint, array $data = []): Response
    {
        return $this->handleResponse($this->client()->post($endpoint, $data));
    }

    /**
     * @throws ConnectionException
     */
    public function PUT(string $endpoint, array $data = []): Response
    {
        return $this->handleResponse($this->client()->put($endpoint, $data));
    }

    /**
     * @throws ConnectionException
     */
    public function PATCH(string $endpoint, array $data = []): Response
    {
        return $this->handleResponse($this->client()->patch($endpoint, $data));
    }

    /**
     * @throws ConnectionException
     */
    public function DELETE(string $endpoint,array $data = []): Response
    {
        return $this->handleResponse($this->client()->delete($endpoint, $data));
    }

    /**
     * @throws ConnectionException
     */
    public function activity(string $activityKey, array $data = []): Response
    {
        return $this->handleResponse($this->client()->post("activity/{$activityKey}", $data));
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
//                'Authorization' => "X-Application-Secret {$this->applicationSecretKey}",
                'X-Application-Secret' => "{$this->applicationSecretKey}",
                'X-Client-Url' => $this->site->url,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }
}
