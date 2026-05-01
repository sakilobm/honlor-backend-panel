
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
