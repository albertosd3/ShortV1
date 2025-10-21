<?php

namespace App\Services;

use GuzzleHttp\Client;

class StopBotService
{
    private Client $client;
    private string $apiKey;

    public function __construct(?Client $client = null)
    {
    $this->client = $client ?: new Client(['timeout' => 3.0]);
    $this->apiKey = (string) config('stopbot.key');
    }

    public function blocker(string $ip, string $ua, string $url): bool
    {
        if (!$this->apiKey) {
            return false;
        }
        $endpoint = 'https://stopbot.net/api/blocker';
        $query = [
            'apikey' => $this->apiKey,
            'ip' => $ip,
            'ua' => $ua,
            'url' => $url,
        ];
        try {
            $res = $this->client->get($endpoint, ['query' => $query]);
            $body = (string) $res->getBody();
            return stripos($body, 'block') !== false || stripos($body, 'denied') !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function ipLookup(string $ip): ?array
    {
        if (!$this->apiKey) {
            return null;
        }
        $endpoint = 'https://stopbot.net/api/iplookup';
        $query = [
            'apikey' => $this->apiKey,
            'ip' => $ip,
        ];
        try {
            $res = $this->client->get($endpoint, ['query' => $query]);
            $json = json_decode((string) $res->getBody(), true);
            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
