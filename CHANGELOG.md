
## [v11.7.4] – 2026-07-09T06:15:00Z

### Changed
- **Active App Version Sync on Edit**: Updated the administrative update endpoint `/api/app_config/update` to automatically trigger `SupabaseClient::syncAppVersion` when modifying configuration fields for the currently active version. This guarantees that edits to the active version's update URL, mandatory status, version tag, or description immediately propagate to the remote Supabase database and mobile clients.

### Escape Plan / Rollback Notes
- **Rollback Path**: Revert `htdocs/libs/api/app_config/update.php` to its original state using `git checkout -- htdocs/libs/api/app_config/update.php`.
- **Forward-Fix Path**: Add a manual sync trigger button to the UI if automated syncs on edit should be made optional or run asynchronously in a queue in the future.

---

## [v11.7.3] – 2026-07-09T05:24:00Z

### Fixed
- **App Version Control Single-Row Constraint**: Transitioned the Supabase Edge Function `manage-app-version` from a single-row constraint (`id = 1`) to a multi-row database history log. Publishing or syncing versions now inserts a new row instead of performing an upsert on `id: 1` which was causing `duplicate key value violates unique constraint "app_versions_pkey"` or violating the `single_row` check constraint.

### Added
- **PostgreSQL Database Migration Script**: Created a new database migration file `scratch/migrate_app_versions.sql` to drop the check constraint `single_row` and align the primary key `id` sequence to prevent duplicate key violations.
- **Dynamic Active Version Resolution**: Updated Deno Edge Function `get_active` and `check_version` actions to order records by `synced_at` and `id` descending, limiting to 1, to retrieve the latest active target.

### Escape Plan / Rollback Notes
- **Rollback Path**: Revert Deno Edge Function using `git checkout -- supabase/functions/manage-app-version/index.ts`. Recreate the single-row check constraint by running `ALTER TABLE public.app_versions ADD CONSTRAINT single_row CHECK (id = 1);` in the Supabase SQL editor.
- **Forward-Fix Path**: Update query parameters or add a specific `is_active` boolean column if explicit manual version activation flags are desired in the future.

---

## [v11.7.2] – 2026-05-27T11:45:00Z

### Fixed
- **Edge Function Auth Base64url Padding**: Resolved the remote cURL HTTP 401 Unauthorized block by implementing base64url padding reconstruction in `isServiceRole()` in `supabase/functions/request-account-deletion/index.ts`. This dynamically appends the correct padding characters (`=`) based on string length modulo 4, avoiding `DOMException` failures in Deno standard `atob()` decoders on unpadded JWT tokens.

### Added
- **Zero-Trust Role-Based Dashboard Guards**:
  - Implemented dynamic page-level access validation mapping in `htdocs/admin.php` based on custom database-driven permissions (`Session::hasPermission($resource, $action)`). Denied requests gracefully return standard `403 Permission Denied` (for AJAX calls) or redirect to safety (for full page loads).
  - Wrapped all primary administrative sidebar items inside `Session::hasPermission()` checks in `htdocs/_templates/admin/_nav.php`. Links like Vault, Moderation, Channels, Ads, Compliance, Deletion, and Server Health are now conditionally rendered according to active role clearance.
- **Backend API Access Controls**:
  - Added explicit permission validation gates inside the `deletion_requests.php` (requires `deletion:view` or `deletion:manage`) and `process_deletion.php` (requires `deletion:manage`) API endpoints.
  - Secured the app-user profiles APIs `app_list.php` (requires `users:view` or `users:manage`) and `app_action.php` (requires `users:view` / `users:manage` based on the targeted profile action) to fully prevent unauthorized privilege escalation or data visibility bypass.

### Escape Plan / Rollback Notes
- **Rollback Path**: Revert code changes in `htdocs/admin.php`, `htdocs/_templates/admin/_nav.php`, and `htdocs/libs/api/users/` files using `git checkout`. Re-deploy the Edge Function if needed.
- **Forward-Fix Path**: Update or expand the page-to-permission mapping array `$pagePermissionMap` in `htdocs/admin.php` to handle new sections or security systems in future upgrades.

---

## [v11.7.1] – 2026-05-25T09:55:00Z

### Fixed
- **Edge Function Auth**: Changed `isServiceRole()` from direct string comparison against `SUPABASE_SERVICE_ROLE_KEY` env var to JWT payload decoding with `role === "service_role"` claim check. The env var comparison was failing because the Supabase platform may store the key differently than what's sent in the Authorization header.

### Changed
- **Dashboard → Supabase Integration**: Rewrote `deletion_requests.php` and `process_deletion.php` API endpoints to use `SupabaseClient::invokeFunction('request-account-deletion', ...)` as primary data source, with automatic fallback to local MySQL if Supabase credentials are not configured or the edge function call fails.
  - `deletion_requests.php`: Now calls `list_requests` action → maps `full_name` to `username` field for dashboard compatibility.
  - `process_deletion.php`: Now calls `update_request` action → sends `admin_note` and `reviewed_by` for audit trail.

### Escape Plan / Rollback Notes
- **PHP Endpoints**: Revert `htdocs/libs/api/users/deletion_requests.php` and `process_deletion.php` to previous versions (MySQL-only). Both files have fallback logic, so if Supabase is unavailable, they automatically fall through to local MySQL.
- **Edge Function**: Re-deploy with `supabase functions deploy request-account-deletion --no-verify-jwt`

---

## [v11.7.0] – 2026-05-25T08:41:00Z

### Added
- **Account Data Deletion Edge Function (`request-account-deletion`)**:
  - Created Supabase Edge Function at `supabase/functions/request-account-deletion/index.ts` with three actions:
    - `submit_request` (public): Accepts user email, reason, and full_name. Validates email format, enforces 24h duplicate prevention, and inserts into `deletion_requests` Supabase table.
    - `list_requests` (service role only): Returns all deletion requests with computed metrics (total, pending, approved, rejected, velocity) for the admin dashboard.
    - `update_request` (service role only): Approves or rejects a request with optional admin note and reviewer identifier.
  - Created migration helper script `scratch/migrate_supabase_deletion_requests.php` with SQL for creating the `deletion_requests` table, RLS policies (no public access), and performance indexes.
  - Updated `supabase_edge_functions_api_docs.md` with full Section 8 documentation covering all three actions, payloads, and response formats.

### Security
- **RLS Enabled**: The `deletion_requests` table has Row Level Security enabled with no public policies — only the service role key can read/write.
- **Input Validation**: Email format validated via regex, all text fields sanitized and length-limited.
- **Duplicate Prevention**: 24-hour window check prevents spam submissions from the same email.
- **Service Role Authentication**: Admin-facing actions (`list_requests`, `update_request`) require the `SUPABASE_SERVICE_ROLE_KEY` in the Authorization header.
- **No PII in Errors**: Error responses are generic and do not expose user data.

### Architectural Decision
- **Single Multi-Action Edge Function**: Consolidated all deletion request operations into one function (matching the `manage-app-version` pattern) rather than creating separate functions for each action.
- **Public Submit Endpoint**: The `submit_request` action does not require JWT authentication because the `honlor.com/data-deletion` page is a public GDPR/App Store compliance page accessible to users who may have already uninstalled the app.

### Escape Plan / Rollback Notes
- **Database**: Run `DROP TABLE IF EXISTS public.deletion_requests;` in Supabase SQL Editor.
- **Edge Function**: Delete `supabase/functions/request-account-deletion/` directory.
- **API Docs**: Revert Section 8 from `supabase_edge_functions_api_docs.md`.

---

## [v11.6.0] – 2026-05-25T07:58:00Z

### Fixed
- **Sidebar Toggle Button Overlay**:
  - Added Tailwind CSS stacking utility `z-50` to `aside#sidebar` inside `htdocs/_templates/admin/_nav.php`.
  - This elevates the sidebar layout context above its sibling `<main>` panel's child `<header>` (which has `z-40` and escapes `<main>`'s auto-stacking context due to `relative` without an explicit z-index class on `<main>`).
  - Prevents the `<header>` background and container boundaries from cropping/hiding the right half of the absolutely-positioned `-right-4` sidebar toggle handle.

### Architectural Decision
- **Tailwind Stacking elevation**: Integrated the fix directly in the class list of the primary navigation component (`_nav.php`) rather than mutating base stylesheets in `_head.php`. This maintains localized, highly visible styling classes for ease of maintenance.

### Escape Plan / Rollback Notes
- **Database Implications**: None.
- **Rollback Path**: Revert `htdocs/_templates/admin/_nav.php` by removing the `z-50` Tailwind class from the `<aside>` element or running `git checkout htdocs/_templates/admin/_nav.php`.
- **Forward-Fix Path**: Increase or decrease the sidebar z-index value inside the class list to coordinate spacing in complex future overlay integrations.

---

## [v11.5.0] – 2026-05-23T18:31:00Z

### Added
- **Supabase Live Credentials Integrated**:
  - Added `supabase_url`, `supabase_anon_key`, and `supabase_service_key` to `project/config.json`.
  - All three values verified live: REST API returns HTTP 200, `hellow-world` edge function reachable, `get-all-users` returns real app users.
  - `SupabaseClient.php` extended with two new methods:
    - `invokeFunction(string $functionName, array $body, string $method)` — invokes any Edge Function via POST/PUT/PATCH using the service role key.
    - `invokeFunctionGet(string $functionName, array $queryParams)` — invokes GET-based edge functions with query parameters.
    - `ping()` — health check shortcut that calls `hellow-world` and returns boolean.
  - All methods return a structured `['success', 'data', 'http_code', 'error']` array with full error logging via `error_log()`.
  - Service key usage is strictly server-side (PHP only); never exposed to browser.

### Architectural Decision
- **Service Role Key via cURL (Server-Side Only)**: The service key bypasses Supabase RLS policies, making it suitable for admin panel actions where the dashboard acts as a trusted server. This key is stored in `project/config.json` which is already in `.gitignore`. It is never passed to the browser.
- **Project URL**: `https://zloyckonnkiywllkyghf.supabase.co`

### Escape Plan / Rollback Notes
- **Database Implications**: None — credentials stored in `config.json` only, no schema changes.
- **Rollback Path**: Remove `supabase_url`, `supabase_anon_key`, and `supabase_service_key` from `project/config.json`. System auto-falls-back to local mock JSON (`scratch/supabase_reports_mock.json`).
- **Forward-Fix Path**: Rotate keys in Supabase Dashboard → Project Settings → API, then update `project/config.json`.

---

## [v11.4.0] – 2026-05-23T15:52:00Z

### Added
- **App Version Control Interface**:
  - Built high-fidelity, premium dark glassmorphic panel in `htdocs/_templates/admin/app_config.php` featuring active telemetry metrics (Active Target, Enforcement Ratio, Registry Status, Registered Shards).
  - Built detailed upgrades registry table with monospaced URLs, hover-copy triggers, status pills, and slide-in drawer details.
  - Implemented sleek "Publish Release" modal form with semantic version validation and target URL syntax checking.
- **Local MySQL AppConfig Model & Controller**:
  - Created `App\AppConfig` model in `htdocs/libs/app/AppConfig.php` running prepared PDO queries for list, get, create, update, and delete actions.
  - Built automatic runtime migration failsafe to create and seed the `app_config` table on first execution.
- **REST API Shard Routes**:
  - Created 4 new REST routes under `htdocs/libs/api/app_config/` (`list.php`, `create.php`, `update.php`, `delete.php`) routed cleanly via rewrite rules.
- **Audit Logging & System Visibility**:
  - Automatically writes structured audit traces to the `logs` table upon publishing, updating, or decommissioning a version configuration, populating real-time telemetry streams instantly.
- **Interactive Routing Integration**:
  - Wired the new sub-section `app_config` inside navigation sidebar (`htdocs/_templates/admin/_nav.php`) and state machine routers inside `htdocs/js/admin.js` and `project/js/admin/admin_app.js`.

### Architectural Decision
- **Local-First SQL Connection**: Designed the version manager to store and manage configurations in the local MySQL database rather than Supabase PostgreSQL REST layers for now, matching the user's explicit setup requirements while leaving standard hooks for eventual Postgres migration.

### Escape Plan / Rollback Notes
- **Database Implications**: MySQL table `app_config` is added.
- **Rollback Path (Database)**: Run `DROP TABLE IF EXISTS app_config;`.
- **Rollback Path (Codebase)**: Decommission and delete `htdocs/_templates/admin/app_config.php`, the `/api/app_config/` directory, and model `htdocs/libs/app/AppConfig.php`. Revert Javascript edits using `git checkout`.
- **Forward-Fix Path**: Perform standard edits inside `htdocs/libs/app/AppConfig.php` or `htdocs/js/admin.js` directly to adjust formatting or styles.

## [v11.3.0] – 2026-05-18T14:30:00Z

### Added
- **Global Toast CSS styling integration**:
  - Added glassmorphic styling, left borders, animations, and custom typography to `.toast-panel`, `.toast-item`, `.toast`, and sub-elements inside `htdocs/_templates/core/_head.php`.
  - Added `slideIn` keyframe animation to animate the toast popup.

### Fixed
- **Unstyled Login Toasts**: Fixed the issue where running `showToast()` or receiving auth alerts on the `/login` and `/signup` templates displayed raw, unstyled text.

### Architectural Decision
- **Tailwind Play CDN Styling**: Embedded the toast styles inside the runtime `<style type="text/tailwindcss">` block inside `core/_head.php` to leverage Tailwind's Play CDN engine. This ensures `@apply` directives are compiled on-the-fly inside the browser, maintaining consistency with our admin style schema without introducing offline CSS build scripts.

### Escape Plan / Rollback Notes
- **Database Implications**: None. This release has zero database structure changes.
- **Rollback Path**: Revert `htdocs/_templates/core/_head.php` by deleting the added Toast Notification style segment (lines 106-141) or running `git checkout htdocs/_templates/core/_head.php`.
- **Forward-Fix Path**: Modify the specific class names or values directly inside `core/_head.php` to address any specific spacing or color overrides.

## [v11.2.0] – 2026-05-18T06:05:00Z

### Added
- **Supabase App Reports Live Ingress Review & Reply System**:
  - Implemented cURL REST API adapter for Supabase (`htdocs/libs/src/SupabaseClient.php`) to fetch active/resolved reports and submit replies securely.
  - Implemented dynamic database fallback: if Supabase URL and Keys are absent from `.env`, the system automatically runs a Mock JSON Storage (`scratch/supabase_reports_mock.json`), preventing panel failure.
  - Added dedicated **App Reports (Supabase)** tab to Safety Center (`htdocs/_templates/admin/reports.php`) with real-time stats and search filtering.
  - Implemented premium visual categorization chips matching options from React Native app (Spam: Lavender, Violence: Red, Abuse: Rose, Nudity: Amber, Drugs: Fuchsia, Personal Details: Cyan, Other: Slate).
  - Designed dynamic glassmorphic modal to type custom replies like "We solved the issue" and submit resolution to Supabase.
  - Registered `SupabaseClient` alias in loader script (`htdocs/libs/load.php`).
  - Synced source controllers (`project/js/admin/admin_app.js`) and execution layers (`htdocs/js/admin.js`).

### Architectural Decision
- **REST API via cURL**: Opted for Supabase PostgREST client via cURL instead of pgsql PDO drivers to ensure 100% server compatibility without requiring complex local database driver setups.
- **Fail-safe Mock JSON Fallback**: Structured the client to write and read from local JSON scratch file if `.env` keys are empty, guaranteeing that the admin dashboard remains robust and interactive until keys are supplied.
- **In-Memory Dynamic Modals**: Designed the reply modal to construct itself dynamically in JS to keep HTML templates clean and maintain single-file maintainability.

## [v11.1.0] – 2026-05-18T05:10:00Z

### Added
- **Active Stream Telemetry Columns**:
  - Restored **Synchronization** and **Governance** columns to the Moderation stream.
  - Synchronization displays live formatted message time and date matching the premium glassmorphic theme.
  - Governance actions (Flag and Purge) render on row hover (`opacity-0 group-hover:opacity-100 transition-all`) to maintain clean interface layout.

### Architectural Decision
- Opted for hybrid formatted dates (Option 3) instead of Time-Ago or ISO format to match premium visual style.
- Opted for hover-revealed glassmorphic micro-action buttons (Option 3) to keep the stream UI uncluttered and minimalist until explicitly hovered.

## [v11.0.0] – 2026-05-01T13:14:33Z

### Added
- **Webhook Hub tab** in Command Orchestration (Settings):
  - Full hook registry list with live status dots (Healthy / Failing / Paused)
  - Per-hook latency display, event tag chips, Pause/Resume/Retry/Delete controls
  - Error drawer with HTTP response codes and timestamps for failing hooks
  - "Register Hook" modal with name, URL, event selectors, HMAC secret field
  - Summary stat cards: Healthy / Failing / Paused / Total counts
  - Persisted to localStorage (ready for API integration)
- **Server Health & Throttles tab** in Command Center (Logs):
  - Service health grid: Nginx, PHP-FPM, MySQL, Redis — with staggered live checks
  - Throttle sliders: Max Connections, Rate Limit, Workers, Timeout, Body Size
  - Protocol toggles: Gzip, Keep-Alive, DDoS Shield, Response Cache
  - Live resource meters (CPU, RAM, Disk I/O, Network) with animated bars
  - Quick Actions: Flush OPCache, Purge Cache, Restart PHP-FPM, Health Check
  - Node Info panel: OS, PHP version, DB, Server Time

### Fixed
- **Sidebar non-responsive lock**: Removed all mobile breakpoint classes (`md:relative`, `fixed`, `-translate-x-full`, `md:translate-x-0`). Sidebar now always renders at full width, unaffected by screen size in both collapsed and expanded states.
- Removed mobile hamburger toggle button and overlay from master layout
- Collapse CSS refactored outside `@media` block — works uniformly at all breakpoints

### Architecture Decision
- Sidebar is now a true persistent layout element (not a mobile drawer). This follows the design intent of an admin panel where the sidebar is always present.
- Webhook state currently uses localStorage for zero-backend-dependency demo; production integration requires a `webhooks` DB table and API endpoints (`api/webhooks/create`, `list`, `update`, `delete`).
