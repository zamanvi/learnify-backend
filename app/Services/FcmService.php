<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected string $projectId;
    protected array $serviceAccount;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->serviceAccount = json_decode(file_get_contents(asset('firebase/masterenglish-f1c79.json')), true);
    }

    public function sendToDevice(string $token, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        $payload = [
            'message' => [
                'token' => $token,
                'data'  => array_merge(['title' => $title, 'body' => $body], $data),
            ],
        ];
        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $payload);
        Log::debug('FCM sendToDevice', ['token' => substr($token, 0, 20), 'response' => $response->body()]);
        return $response->successful();
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();
        $payload = [
            'message' => [
                'topic' => $topic,
                'data' => array_merge([
                    'title' => $title,
                    'body' => $body,
                ], $data),
            ],
        ];
        $response = Http::withToken($accessToken)->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $payload);
        Log::debug('FCM body', ['response' => $topic . ' ' . $title . ' ' . $body]);
        Log::debug('FCM Response', ['response' => $response->body()]);
        return $response->successful();

    }

    private function getAccessToken(): string
    {
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();

        $claimSet = base64_encode(json_encode([
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signatureInput = "{$header}.{$claimSet}";

        openssl_sign($signatureInput, $signature, $this->serviceAccount['private_key'], 'SHA256');
        $jwt = "{$header}.{$claimSet}." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json()['access_token'];
    }
}
