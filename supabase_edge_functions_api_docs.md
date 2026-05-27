# Honlor Supabase Edge Functions API Documentation

This document serves as the comprehensive API reference for the Honlor Supabase Edge Functions. It is specifically designed to be ingested by the Honlor Dashboard AI to facilitate seamless integration, bridge features, and connect to the Supabase database.

All endpoints share the base URL structure:
`https://<your-supabase-project-id>.supabase.co/functions/v1/<function-name>`

## Authentication & Headers
Unless stated otherwise, all requests should include the following headers:
- `Content-Type: application/json`
- `Authorization: Bearer <USER_JWT_TOKEN>` (For endpoints requiring user authentication, ensure the JWT token corresponds to a valid user session).

---

## 1. User & Profile Management

### 1.1 `update-profile`
Manages user profiles, onboarding, and avatar updates using discriminated payloads.
* **Method:** `POST`
* **Payloads (Discriminated by `action`):**
  * `create_profile`: `{ "action": "create_profile", "full_name": "string", "email": "string", "avatar_url": "string" }`
  * `update_settings`: `{ "action": "update_settings", "settings": {} }`
  * `update_profile_setup`: `{ "action": "update_profile_setup", "full_name": "string", "username": "string", "avatar_url": "string", "onboarding_completed": boolean }`
  * `update_avatar`: `{ "action": "update_avatar", "avatar_url": "string" }`
  * `complete_onboarding`: `{ "action": "complete_onboarding" }`
* **Response:** `{ "success": true }`

### 1.2 `get-all-users`
Retrieves a list of all users. Utilizes the Service Role key internally to bypass RLS.
* **Method:** `GET` / `POST` (Treated functionally identically as it ignores body)
* **Payload:** None
* **Response:**
  ```json
  {
    "success": true,
    "users": [
      { "id": "uuid", "full_name": "string", "avatar_url": "string", "email": "string" }
    ]
  }
  ```

### 1.3 `check-email`
Verifies if an email address format is valid and ensures the domain has MX records to receive emails (blocks disposable domains).
* **Method:** `POST`
* **Payload:** `{ "email": "user@example.com" }`
* **Response:** `{ "success": true, "deliverable": true/false, "exists": false, "reason": "..." }`

### 1.4 `link-oauth`
Migrates user application rows (messages, posts, profiles) from an OAuth profile to a Primary Email profile, avoiding duplicate accounts.
* **Method:** `POST`
* **Payload:** `{ "oauthUserId": "uuid", "email": "user@example.com", "action": "migrate" | "migrate_and_delete" }`
* **Response:** `{ "merged": true, "primaryId": "uuid", "results": [...] }`

### 1.5 `generate-token`
Generates a StreamChat JWT token securely for the authenticated user to establish a WebSocket connection.
* **Method:** `POST`
* **Payload:** None (Requires `Authorization` header)
* **Response:** `{ "token": "string", "user_id": "uuid", "api_key": "string" }`

---

## 2. Friends & Connections

### 2.1 `manage-friends`
Manages friend requests and blocking using discriminated payloads.
* **Method:** `POST`
* **Payloads (Discriminated by `action`):**
  * `send_request`: `{ "action": "send_request", "receiverId": "uuid" }`
  * `accept_request`: `{ "action": "accept_request", "requestId": "uuid", "senderId": "uuid" }`
  * `reject_request`: `{ "action": "reject_request", "requestId": "uuid" }`
  * `unfriend`: `{ "action": "unfriend", "friendId": "uuid" }`
  * `block_user`: `{ "action": "block_user", "blockedId": "uuid" }`
  * `unblock_user`: `{ "action": "unblock_user", "blockedId": "uuid" }`

---

## 3. Channels & Messaging

### 3.1 `create-channel`
Creates a new Stream channel and logs it into the internal Supabase database.
* **Method:** `POST`
* **Payload:** `{ "name": "string", "description": "string", "image_url": "string", "image_path": "string", "is_private": boolean, "user_id": "uuid", "public_link": "string", "member_ids": ["uuid"] }`

### 3.2 `delete-channel`
Deletes a specific channel entirely.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string" }`

### 3.3 `delete-dm`
Deletes a Direct Message (DM) channel between members.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "memberIds": ["uuid", "uuid"] }`

### 3.4 `search-public-channels`
Searches for public channels via query string.
* **Method:** `POST`
* **Payload:** `{ "query": "string", "userId": "uuid" }`

### 3.5 `toggle-channel-privacy`
Changes a channel's visibility state between public and private.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "make_private": boolean }`

### 3.6 `transfer-channel-ownership`
Transfers the owner role of a channel to another user.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "newOwnerId": "uuid" }`

### 3.7 `add-channel-members`
Adds a batch of users to an existing channel.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "memberIds": ["uuid"] }`

### 3.8 `change-channel-role`
Updates a specific user's role within a channel.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "targetUserId": "uuid", "role": "string" }`

---

## 4. Channel Links & Invites

### 4.1 `create-channel-link`
Generates an invite link for a channel to be shared externally.
* **Method:** `POST`
* **Payload:** `{ "channel_cid": "string", "link_type": "string", "public_link": "string" }`

### 4.2 `delete-channel-link`
Deletes an active channel invite link.
* **Method:** `POST`
* **Payload:** `{ "channel_cid": "string", "token": "string" }` (Accepts generic body data tied to the link)

### 4.3 `revoke-channel-link`
Revokes an existing invite link, preventing future joins via that link.
* **Method:** `POST`
* **Payload:** `{ "channel_cid": "string" }`

### 4.4 `get-channel-details-for-link`
Fetches public details of a channel for landing pages prior to joining.
* **Method:** `GET`
* **Query Parameters:** `?link=<linkIdentifier>`
* **Response:** `{ "name": "string", "image": "string", "member_count": number }`

### 4.5 `handle-join-request`
Processes a request for a user attempting to join a private or gated channel.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "userId": "uuid", "action": "direct" | "string" }`

### 4.6 `join-channel`
Directly joins a user to a channel via an invite token.
* **Method:** `POST`
* **Payload:** `{ "token": "string", "channelId": "string", "userId": "uuid" }`

---

## 5. Moderation & Reporting

### 5.1 `submit-report`
Submits feedback about the app or reports another user.
* **Method:** `POST`
* **Payloads (Discriminated by `type`):**
  * `app_feedback`: `{ "type": "app_feedback", "category": "string", "details": "string" }`
  * `user_report`: `{ "type": "user_report", "reportedId": "uuid", "reason": "string", "details": "string", "channelId": "string" }`

### 5.2 `manage-reports`
Allows users (or administrators) to delete, clear, or cancel their reports/feedbacks.
* **Method:** `POST`
* **Payloads (Discriminated by `action`):**
  * `delete_single`: `{ "action": "delete_single", "reportId": "uuid" }`
  * `clear_history`: `{ "action": "clear_history" }`
  * `cancel_all`: `{ "action": "cancel_all" }`

### 5.3 `send-audit-log`
Sends audit/admin logging strings to a specified channel to track major administrative events.
* **Method:** `POST`
* **Payload:** `{ "channelId": "string", "channelType": "messaging", "text": "string" }`

---

## 6. App Version Control

### 6.1 `manage-app-version`
Manages the active app version for client update notifications. Called by the Admin Panel on the server side (`sync_active`) and by the mobile app on launch (`get_active`, `check_version`). JWT verification is **disabled** — the `sync_active` action performs its own service-key check internally.

* **Base URL:** `https://zloyckonnkiywllkyghf.supabase.co/functions/v1/manage-app-version`

#### Action: `get_active`
Returns the currently active version record. Used by the mobile app to display version info.
* **Method:** `GET` (query param `?action=get_active`) or `POST`
* **Auth:** None required
* **Response:**
  ```json
  { "success": true, "version": "v1.2.3", "is_mandatory": true, "update_url": "https://...", "description": "...", "synced_at": "2025-01-01T00:00:00Z" }
  ```

#### Action: `check_version`
Compares the client's current version against the active version and returns whether an update is needed.
* **Method:** `POST`
* **Auth:** None required
* **Payload:** `{ "action": "check_version", "current_version": "v1.0.0" }`
* **Response:**
  ```json
  { "success": true, "needs_update": true, "is_mandatory": true, "latest_version": "v1.2.3", "update_url": "https://...", "description": "..." }
  ```
  When no update is needed: `{ "success": true, "needs_update": false, "is_mandatory": false, "latest_version": "v1.2.3", "update_url": null, "description": null }`

#### Action: `sync_active`
Upserts the active version to the `app_versions` Supabase table. **Called only by the Admin Panel server** — requires the `SUPABASE_SERVICE_ROLE_KEY` in the `Authorization` header.
* **Method:** `POST`
* **Auth:** `Authorization: Bearer <SERVICE_ROLE_KEY>`
* **Payload:** `{ "action": "sync_active", "version": "v1.2.3", "is_mandatory": true, "update_url": "https://...", "description": "Release notes" }`
* **Response:** `{ "success": true, "message": "Version v1.2.3 synced as active build target." }`
* **Admin Panel Integration:** Called automatically by `SupabaseClient::syncAppVersion()` whenever an admin clicks "Set Active" or publishes a new version with "Set as Active Build Target" toggled on.

---

## 8. Account Data Deletion (GDPR/App Store Compliance)

### 8.1 `request-account-deletion`
Handles user data deletion requests for GDPR and App Store compliance. Called by the public `honlor.com/data-deletion` page (for submissions) and by the Admin Panel server (for listing and processing).

* **Base URL:** `https://zloyckonnkiywllkyghf.supabase.co/functions/v1/request-account-deletion`

#### Action: `submit_request`
Submits a new account deletion request. Public — no authentication required.
* **Method:** `POST`
* **Auth:** None required
* **Payload:**
  ```json
  {
    "action": "submit_request",
    "email": "user@example.com",
    "reason": "I no longer use this app",
    "full_name": "John Doe"
  }
  ```
  * `email` (required): User's account email address, max 255 chars.
  * `reason` (optional): Why the user wants deletion, max 1000 chars.
  * `full_name` (optional): User's display name, max 100 chars.
* **Response (Success):**
  ```json
  { "success": true, "message": "Your account deletion request has been submitted successfully. Our team will review it within 30 days." }
  ```
* **Response (Duplicate):** `409` — A pending request for this email already exists within 24h.
* **Response (Invalid Email):** `400` — Email format validation failed.

#### Action: `list_requests`
Returns all deletion requests with computed metrics. **Admin Panel only** — requires Service Role Key.
* **Method:** `POST`
* **Auth:** `Authorization: Bearer <SERVICE_ROLE_KEY>`
* **Payload:** `{ "action": "list_requests" }`
* **Response:**
  ```json
  {
    "success": true,
    "requests": [
      {
        "id": "uuid",
        "email": "user@example.com",
        "full_name": "John Doe",
        "reason": "I no longer use this app",
        "status": "pending",
        "admin_note": null,
        "created_at": "2026-05-25T08:00:00Z",
        "reviewed_at": null,
        "reviewed_by": null
      }
    ],
    "stats": {
      "total": 5,
      "pending": 3,
      "approved": 1,
      "rejected": 1,
      "velocity": 40.0
    }
  }
  ```

#### Action: `update_request`
Approves or rejects a deletion request. **Admin Panel only** — requires Service Role Key.
* **Method:** `POST`
* **Auth:** `Authorization: Bearer <SERVICE_ROLE_KEY>`
* **Payload:**
  ```json
  {
    "action": "update_request",
    "id": "uuid",
    "status": "approved",
    "admin_note": "User data purged from all systems.",
    "reviewed_by": "admin_username"
  }
  ```
  * `id` (required): UUID of the deletion request.
  * `status` (required): `"approved"` or `"rejected"`.
  * `admin_note` (optional): Internal admin note, max 500 chars.
  * `reviewed_by` (optional): Admin identifier, max 100 chars.
* **Response:** `{ "success": true, "message": "Deletion request approved successfully." }`

---

## 9. Miscellaneous

### 9.1 `hellow-world`
Simple health-check or testing endpoint.
* **Method:** `POST` / `GET`
* **Response:** Typically returns a basic hello message.

---

> **Implementation Note for the Honlor Dashboard AI:**
> * When invoking these functions via the `supabase-js` client, use `supabase.functions.invoke('function-name', { body: { ... } })`.
> * The `Authorization` header is automatically appended by the Supabase client if a user is authenticated.
> * For endpoints that require Server/Service Role permissions (e.g., global administrative actions), the Edge Functions are already configured internally to use `createSupabaseServiceRoleClient()`. You do not need to pass the Service Key from the Dashboard; just authenticate the Admin user properly and the Edge Function will validate their session.
