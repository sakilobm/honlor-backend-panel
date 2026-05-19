<?php

namespace Aether;

/**
 * Supabase client and fallback sync layer.
 * ========================================
 * PSR-4 Namespace: Aether\SupabaseClient
 */
class SupabaseClient
{
    private static function getMockFile(): string
    {
        return HTDOCS_ROOT . '/scratch/supabase_reports_mock.json';
    }

    /**
     * Fetch reports from Supabase (or fallback to local mock).
     */
    public static function getReports(): array
    {
        $url = get_config('supabase_url');
        $key = get_config('supabase_anon_key');

        if (empty($url) || empty($key)) {
            // Credentials not defined - fallback to local JSON mock
            $file = self::getMockFile();
            if (file_exists($file)) {
                return json_decode(file_get_contents($file), true) ?? [];
            }
            return [];
        }

        // Fetch from Supabase REST API
        $endpoint = rtrim($url, '/') . '/rest/v1/reports?select=*&order=created_at.desc';
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: {$key}",
            "Authorization: Bearer {$key}",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true) ?? [];
        }

        // Log error and fallback
        error_log("Supabase REST API Error (HTTP {$httpCode}): " . $response);
        $file = self::getMockFile();
        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true) ?? [];
        }
        return [];
    }

    /**
     * Update a report status and admin reply (Supabase or local mock).
     */
    public static function updateReport(string $id, string $status, ?string $reply): bool
    {
        $url = get_config('supabase_url');
        $key = get_config('supabase_anon_key');
        $resolvedAt = date('c'); // ISO 8601 timestamp

        if (empty($url) || empty($key)) {
            // Credentials not defined - update local JSON mock
            $file = self::getMockFile();
            if (file_exists($file)) {
                $reports = json_decode(file_get_contents($file), true) ?? [];
                $updated = false;
                foreach ($reports as &$r) {
                    if ($r['id'] === $id) {
                        $r['status'] = $status;
                        $r['admin_reply'] = $reply;
                        $r['resolved_at'] = $resolvedAt;
                        $updated = true;
                        break;
                    }
                }
                if ($updated) {
                    file_put_contents($file, json_encode($reports, JSON_PRETTY_PRINT));
                    return true;
                }
            }
            return false;
        }

        // Update in Supabase via PATCH
        $endpoint = rtrim($url, '/') . "/rest/v1/reports?id=eq.{$id}";
        $payload = json_encode([
            'status' => $status,
            'admin_reply' => $reply,
            'resolved_at' => $resolvedAt
        ]);

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: {$key}",
            "Authorization: Bearer {$key}",
            "Content-Type: application/json",
            "Prefer: return=minimal"
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 300);
    }
}
