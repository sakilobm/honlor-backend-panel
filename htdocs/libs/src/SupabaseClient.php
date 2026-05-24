<?php

namespace Aether;

/**
 * Supabase client and fallback sync layer.
 * ========================================
 * PSR-4 Namespace: Aether\SupabaseClient
 *
 * Credentials are loaded via get_config() which reads from:
 *   1. Environment variables (SUPABASE_URL, SUPABASE_ANON_KEY, SUPABASE_SERVICE_KEY)
 *   2. project/config.json (supabase_url, supabase_anon_key, supabase_service_key)
 *
 * Project: https://zloyckonnkiywllkyghf.supabase.co
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

    // =========================================================
    // Edge Function Invocation Layer
    // =========================================================

    /**
     * Invoke a Supabase Edge Function using the Service Role Key.
     *
     * The service key bypasses all RLS policies — suitable for
     * admin-panel server-side calls ONLY. Never expose to browser.
     *
     * @param  string $functionName  Edge function name (e.g. 'create-channel')
     * @param  array  $body          JSON payload to send
     * @param  string $method        HTTP method (default: POST)
     * @return array                 ['success' => bool, 'data' => array, 'http_code' => int, 'error' => string|null]
     */
    public static function invokeFunction(string $functionName, array $body = [], string $method = 'POST'): array
    {
        $url        = get_config('supabase_url');
        $serviceKey = get_config('supabase_service_key');

        if (empty($url) || empty($serviceKey)) {
            error_log("SupabaseClient::invokeFunction — supabase_url or supabase_service_key not configured.");
            return [
                'success'   => false,
                'data'      => null,
                'http_code' => 0,
                'error'     => 'Supabase credentials not configured.',
            ];
        }

        $endpoint = rtrim($url, '/') . '/functions/v1/' . ltrim($functionName, '/');

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $serviceKey,
            'apikey: '              . $serviceKey,
        ]);

        if (!empty($body) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log("SupabaseClient::invokeFunction [{$functionName}] cURL error: {$curlError}");
            return ['success' => false, 'data' => null, 'http_code' => 0, 'error' => $curlError];
        }

        $decoded = json_decode($response, true);
        $success = ($httpCode >= 200 && $httpCode < 300);

        if (!$success) {
            error_log("SupabaseClient::invokeFunction [{$functionName}] HTTP {$httpCode}: {$response}");
        }

        return [
            'success'   => $success,
            'data'      => $decoded,
            'http_code' => $httpCode,
            'error'     => $success ? null : ($decoded['error'] ?? $decoded['message'] ?? "HTTP {$httpCode}"),
        ];
    }

    /**
     * Invoke a Supabase Edge Function via GET with query parameters.
     *
     * @param string $functionName  Edge function name
     * @param array  $queryParams   Key-value pairs appended as ?key=value
     */
    public static function invokeFunctionGet(string $functionName, array $queryParams = []): array
    {
        $url        = get_config('supabase_url');
        $serviceKey = get_config('supabase_service_key');

        if (empty($url) || empty($serviceKey)) {
            return ['success' => false, 'data' => null, 'http_code' => 0, 'error' => 'Supabase credentials not configured.'];
        }

        $endpoint = rtrim($url, '/') . '/functions/v1/' . ltrim($functionName, '/');
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $serviceKey,
            'apikey: '              . $serviceKey,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'data' => null, 'http_code' => 0, 'error' => $curlError];
        }

        $decoded = json_decode($response, true);
        $success = ($httpCode >= 200 && $httpCode < 300);

        return [
            'success'   => $success,
            'data'      => $decoded,
            'http_code' => $httpCode,
            'error'     => $success ? null : ($decoded['error'] ?? $decoded['message'] ?? "HTTP {$httpCode}"),
        ];
    }

    /**
     * Sync the active app version to Supabase via the manage-app-version edge function.
     *
     * Called by the admin panel whenever an admin sets a version as active or
     * publishes a new version with auto-activate enabled.
     *
     * The mobile app will then call get_active / check_version on the same
     * edge function to determine if an update is required.
     *
     * @param  string $version      Semantic version string, e.g. "v1.2.3"
     * @param  bool   $isMandatory  Whether the update is mandatory
     * @param  string $updateUrl    Download / store URL
     * @param  string $description  Release notes
     * @return array                ['success' => bool, 'error' => string|null]
     */
    public static function syncAppVersion(
        string $version,
        bool   $isMandatory,
        string $updateUrl,
        string $description = ''
    ): array {
        return self::invokeFunction('manage-app-version', [
            'action'       => 'sync_active',
            'version'      => $version,
            'is_mandatory' => $isMandatory,
            'update_url'   => $updateUrl,
            'description'  => $description,
        ]);
    }

    /**
     * Quick health check: ping the hellow-world function to verify connectivity.
     * Returns true if the edge runtime is reachable.
     */
    public static function ping(): bool
    {
        $result = self::invokeFunction('hellow-world', [], 'POST');
        return $result['success'];
    }
}
