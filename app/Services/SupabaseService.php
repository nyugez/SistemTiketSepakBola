<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected string $url;
    protected string $key;
    protected string $serviceRoleKey;
    protected string $defaultBucket;

    public function __construct()
    {
        $this->url = rtrim((string) config('supabase.url'), '/');
        $this->key = (string) config('supabase.key');
        $this->serviceRoleKey = (string) config('supabase.service_role_key');
        $this->defaultBucket = (string) config('supabase.storage.bucket', 'tickets');
    }

    /**
     * Dapatkan Public URL dari berkas yang tersimpan di Supabase Storage
     */
    public function getPublicUrl(string $path, ?string $bucket = null): string
    {
        $bucket = $bucket ?: $this->defaultBucket;
        $path = ltrim($path, '/');

        return "{$this->url}/storage/v1/object/public/{$bucket}/{$path}";
    }

    /**
     * Upload berkas ke Supabase Storage (e.g. tiket PDF, barcode, gambar)
     */
    public function uploadFile(string $path, string $fileContents, string $mimeType = 'application/pdf', ?string $bucket = null): bool
    {
        $bucket = $bucket ?: $this->defaultBucket;
        $path = ltrim($path, '/');
        $endpoint = "{$this->url}/storage/v1/object/{$bucket}/{$path}";

        // Prioritaskan service_role_key untuk izin tulis, atau gunakan anon key
        $authToken = !empty($this->serviceRoleKey) ? $this->serviceRoleKey : $this->key;

        try {
            $response = Http::withHeaders([
                'apikey'        => $this->key,
                'Authorization' => "Bearer {$authToken}",
                'Content-Type'  => $mimeType,
                'x-upsert'      => 'true',
            ])->withBody($fileContents, $mimeType)->post($endpoint);

            if ($response->successful()) {
                return true;
            }

            Log::error('Supabase upload error: ' . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error('Supabase upload exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hapus berkas dari Supabase Storage
     */
    public function deleteFile(string $path, ?string $bucket = null): bool
    {
        $bucket = $bucket ?: $this->defaultBucket;
        $path = ltrim($path, '/');
        $endpoint = "{$this->url}/storage/v1/object/{$bucket}/{$path}";

        $authToken = !empty($this->serviceRoleKey) ? $this->serviceRoleKey : $this->key;

        try {
            $response = Http::withHeaders([
                'apikey'        => $this->key,
                'Authorization' => "Bearer {$authToken}",
            ])->delete($endpoint);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Supabase delete exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil raw content berkas dari Supabase Storage
     */
    public function downloadFile(string $path, ?string $bucket = null): ?string
    {
        $bucket = $bucket ?: $this->defaultBucket;
        $path = ltrim($path, '/');
        $endpoint = "{$this->url}/storage/v1/object/authenticated/{$bucket}/{$path}";

        $authToken = !empty($this->serviceRoleKey) ? $this->serviceRoleKey : $this->key;

        try {
            $response = Http::withHeaders([
                'apikey'        => $this->key,
                'Authorization' => "Bearer {$authToken}",
            ])->get($endpoint);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable $e) {
            Log::error('Supabase download exception: ' . $e->getMessage());
            return null;
        }
    }
}
