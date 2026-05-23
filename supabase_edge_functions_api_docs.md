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

## 6. Miscellaneous

### 6.1 `hellow-world`
Simple health-check or testing endpoint.
* **Method:** `POST` / `GET`
* **Response:** Typically returns a basic hello message.

---

> **Implementation Note for the Honlor Dashboard AI:**
> * When invoking these functions via the `supabase-js` client, use `supabase.functions.invoke('function-name', { body: { ... } })`.
> * The `Authorization` header is automatically appended by the Supabase client if a user is authenticated.
> * For endpoints that require Server/Service Role permissions (e.g., global administrative actions), the Edge Functions are already configured internally to use `createSupabaseServiceRoleClient()`. You do not need to pass the Service Key from the Dashboard; just authenticate the Admin user properly and the Edge Function will validate their session.
