# Architecture Documentation

## Overview
The Honlor Admin Panel serves as the central nervous system for moderation, telemetries, and system analytics. This document outlines the architectural components of the moderation pipeline, focusing on the newly integrated **Supabase App Reports Live Ingress System**.

---

## System Components

### 1. Client App (React Native)
- The end-user application (built using React Native, Expo, and TypeScript).
- Includes the `ReportModal` component which captures user/channel reports.
- Dispatches and stores raw reports into **Supabase database (`public.reports`)**.

### 2. Supabase Integration Layer (PostgreSQL & REST)
- Stores report packets inside the `public.reports` table (with columns: `id`, `reporter_id`, `reported_id`, `channel_id`, `reason`, `details`, `status`, `created_at`, `admin_reply`, `resolved_at`).
- Serves endpoints via PostgREST to retrieve and patch rows.

### 3. Aether Backend Framework (PHP Engine)
- Serves as the middle-tier controller and JSON API engine.
- Implements `Aether\SupabaseClient` (`htdocs/libs/src/SupabaseClient.php`):
  - Fetches the active reports queue using standard HTTP cURL requests.
  - Submits resolution status (`resolved` or `dismissed`) and records the custom `admin_reply` response text in Supabase.
  - **Dynamic Fail-Safe:** If `SUPABASE_URL` is not yet configured in `.env`, the adapter silently switches to a local scratch JSON storage file (`scratch/supabase_reports_mock.json`) to keep the dashboard running seamlessly without exceptions.

### 4. Admin Frontend Dashboard (HTML / Vanilla CSS / JS)
- Displays the **Safety Center** which contains a tabbed interface:
  1. **Incident Queue:** Local system alerts.
  2. **Resolution History:** Local system logs.
  3. **App Reports (Supabase):** Dedicated live ingress telemetry showing app-originated reports.
- Implements a stunning, dynamic reply modal to type custom messages and mark reports as solved in the Supabase backend.
- Provides search filters to filter reports in real time on the client side.

### 5. App Version Control Module (Governance Shards)
- **Database Table:** Interacts with the `app_config` table (containing `id`, `latest_version`, `is_mandatory`, `update_url`, `description`, `updated_at`).
- **PHP App Controller:** Implements `App\AppConfig` (`htdocs/libs/app/AppConfig.php`) to run database queries for version management, validation, and logs.
- **REST Endpoints:** Exposes `/api/app_config/` REST routes:
  - `list.php`: Returns the version queue and calculates key metrics (Enforcement Ratio, total releases, sync status).
  - `create.php`: Validates and registers new client version binary upgrades.
  - `update.php`: Updates metadata details or flips mandatory flags.
  - `delete.php`: Safely purges obsolete release lines.
- **Interactive UI:** Placed under **Governance** named **App Version Control**, showing real-time stats cards, custom switch toggles for instant enforcement, slide-out edit drawers, and custom modal inputs.

### 6. Account Data Deletion Module (GDPR/App Store Compliance)
- **Edge Function:** `request-account-deletion` (`supabase/functions/request-account-deletion/index.ts`) handles three actions:
  - `submit_request` (public): Accepts email, reason, and full_name from the user-facing `honlor.com/data-deletion` page. Validates email format, enforces 24h duplicate prevention, and inserts into the Supabase `deletion_requests` table.
  - `list_requests` (service role only): Returns all deletion requests with computed metrics for the admin dashboard.
  - `update_request` (service role only): Approves or rejects requests with optional admin notes.
- **Database Table:** `public.deletion_requests` in Supabase PostgreSQL with RLS enabled (no public policies — service role only). Contains columns: `id` (UUID), `email`, `full_name`, `reason`, `status`, `admin_note`, `created_at`, `reviewed_at`, `reviewed_by`.
- **Admin Dashboard Integration:** The existing **Identity Erasure** section (`htdocs/_templates/admin/deletion_requests.php`) fetches and displays these requests via `SupabaseClient` using the service role key.


### 7. Zero-Trust Role-Based Access Control (RBAC)
- **Role Studio Model**: Implements `App\Role` (`htdocs/libs/app/Role.php`) which manages administrative roles and granular permissions.
- **Permission Checking**: Uses `Session::hasPermission(string $resource, string $action)` inside `htdocs/libs/src/Session.php` to check if a user is a Master Admin (absolute bypass) or has the specified privilege mapped in their role permissions matrix JSON.
- **Dashboard Routing Guard**: `htdocs/admin.php` maps all administrative pages (Identity Vault, Moderation, Channels, Ads Pipeline, Compliance, Account Deletion, Server Health, Intelligence, and Policy Studio) to system resources. Unauthenticated or unauthorized page requests are blocked and either redirected back to safety (`page=dashboard`) or returned as `403 Permission Denied` (AJAX).
- **Navigation Visibility**: Sidebar anchors in `htdocs/_templates/admin/_nav.php` are wrapped dynamically inside `Session::hasPermission()` checks to present a clean interface tailored exclusively to the logged-in agent's authorization level.
- **Server API Security Gates**: All administrative user APIs in `libs/api/users/` (app user profile updates, lists, and account deletion requests) enforce server-side permission gates to fully prevent privilege escalation or horizontal data visibility bypasses.

---

## Data Flow Diagram
For the visual Mermaid representation of components, refer to [ARCHITECTURE_DIAGRAM.mmd](file:///var/www/html/honlor-admin-panel/ARCHITECTURE_DIAGRAM.mmd).
