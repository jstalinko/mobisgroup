<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\GuzzleException;

trait ServiceTrait
{
    protected string $jdigital_api_url;
    protected string $jdigital_apikey;
    protected Client $httpClient;

    // TTL cache dalam detik (default 5 menit)
    protected int $cacheTtl = 60 * 30;

    public function bootService(): void
    {
        $this->jdigital_api_url = 'https://dracin.javaradigital.com/api/v1/';
        $this->jdigital_apikey   = env('JDIGITAL_APIKEY');

        $this->httpClient = new Client([
            'base_uri' => $this->jdigital_api_url,
            'timeout'  => 20,
        ]);
    }

    public function http(string $path, string $method = 'GET', array $options = [])
    {
        $method = strtoupper($method);
        $cleanPath = ltrim($path, '/');

        // Buat cache key unik
        $cacheKey = $this->makeCacheKey($cleanPath, $method, $options);

        // Ambil dari cache jika ada
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Inject API Key
        $options['headers']['X-JDigital-Apikey'] = $this->jdigital_apikey;
        $options['headers']['Accept'] = 'application/json';

        try {

            $response = $this->httpClient->request($method, $cleanPath, $options);
            $data = json_decode($response->getBody()->getContents(), true);

            // Cache hanya jika ada success=true
            if (isset($data['success']) && $data['success'] === true) {
                Cache::put($cacheKey, $data, $this->cacheTtl);
            }

            return $data;

        } catch (GuzzleException $e) {
            // Biar ke-handle di controller
            throw $e;
        }
    }

    protected function makeCacheKey(string $path, string $method, array $options): string
    {
        // serialize options biar key unik meski beda query/body
        $optHash = md5(json_encode($options));

        return sprintf(
            "cached_request_:%s:%s:%s",
            strtolower($method),
            md5($path),
            $optHash
        );
    }
}
