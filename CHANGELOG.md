
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
