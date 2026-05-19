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

---

## Data Flow Diagram
For the visual Mermaid representation of components, refer to [ARCHITECTURE_DIAGRAM.mmd](file:///var/www/html/honlor-admin-panel/ARCHITECTURE_DIAGRAM.mmd).
