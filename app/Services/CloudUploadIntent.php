<?php

namespace App\Services;

use App\Models\User;

class CloudUploadIntent
{
    public function enabled(): bool
    {
        return config('services.project_uploads.direct_driver') === 'vercel_blob'
            && filled($this->secret());
    }

    public function formConfig(?User $user): ?array
    {
        if (! $user || ! $this->enabled()) {
            return null;
        }

        return [
            'driver' => 'vercel_blob',
            'handleUrl' => config('services.project_uploads.blob_handle_url'),
            'access' => config('services.project_uploads.blob_access', 'public'),
            'maxBytes' => (int) config('services.project_uploads.max_bytes', 104857600),
            'intent' => $this->make($user),
        ];
    }

    public function make(User $user): string
    {
        $payload = $this->base64UrlEncode(json_encode([
            'user_id' => $user->id,
            'exp' => now()->addMinutes(30)->timestamp,
            'max_bytes' => (int) config('services.project_uploads.max_bytes', 104857600),
            'content_types' => ['application/pdf'],
        ], JSON_THROW_ON_ERROR));

        return $payload.'.'.$this->signature($payload);
    }

    private function signature(string $payload): string
    {
        return $this->base64UrlEncode(hash_hmac('sha256', $payload, $this->secret(), true));
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function secret(): string
    {
        return (string) config('services.project_uploads.secret', '');
    }
}
