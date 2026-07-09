# Supabase Edge Function Documentation: `manage-app-version`

This Edge Function acts as the version management API for the Honlor application. It coordinates both check-for-updates requests from mobile apps and version updates sent by administrative panels.

* **Endpoint URL:** `https://zloyckonnkiywllkyghf.supabase.co/functions/v1/manage-app-version`
* **HTTP Methods:** `GET`, `POST`, `OPTIONS` (supports CORS)

---

## Actions Summary

| Action | HTTP Method | Auth Level | Description |
| :--- | :--- | :--- | :--- |
| [`get_active`](#1-get-active-version) | `GET` or `POST` | Public (No auth) | Returns active version details currently set in the database (resolves to the latest synced entry). |
| [`check_version`](#2-check-client-version) | `POST` | Public (No auth) | Compares a client's version against the database to check if an update is needed. |
| [`sync_active`](#3-syncupdate-active-version) | `POST` | Authorized (Admin Only) | Inserts the latest version profile into the database history log. |

---

## 1. Get Active Version
Used to retrieve details of the currently active version (resolves to the latest synced entry).

* **Request Example (GET):**
  ```http
  GET /functions/v1/manage-app-version?action=get_active HTTP/1.1
  Host: zloyckonnkiywllkyghf.supabase.co
  ```

* **Success Response (`200 OK`):**
  ```json
  {
    "success": true,
    "version": "1.0.42",
    "is_mandatory": false,
    "update_url": "https://play.google.com/store/apps/details?id=com.honlor.app",
    "description": "Premium space themes, layouts, and keyboard avoiding fixes.",
    "synced_at": "2026-07-09T05:00:00.000Z"
  }
  ```

---

## 2. Check Client Version
Called by the mobile app on startup to determine if it needs to prompt the user to update.

* **Request Example:**
  ```http
  POST /functions/v1/manage-app-version HTTP/1.1
  Host: zloyckonnkiywllkyghf.supabase.co
  Content-Type: application/json

  {
    "action": "check_version",
    "current_version": "1.0.40"
  }
  ```

* **Success Response (`200 OK`):**
  ```json
  {
    "success": true,
    "needs_update": true,
    "is_mandatory": false,
    "latest_version": "1.0.42",
    "update_url": "https://play.google.com/store/apps/details?id=com.honlor.app",
    "description": "Premium space themes, layouts, and keyboard avoiding fixes."
  }
  ```

---

## 3. Sync/Update Active Version
Called by your Admin Panel Dashboard to deploy a new version target by inserting a new history record.

### Authentication Modes
You can authorize this action in two ways:

1. **Option A: User Session Token (Recommended for Dashboard Frontends)**
   Pass the logged-in administrator's active access token in the `Authorization` header. The Edge Function runs queries using this token, respecting Row Level Security (RLS) policies set on the `app_versions` table.
   * **Header Format:** `Authorization: Bearer <USER_SESSION_ACCESS_TOKEN>`

2. **Option B: Service Role Key (Recommended for Build Scripts/CLI)**
   Pass the project's secret `SERVICE_ROLE_KEY` to bypass all RLS rules. **Never expose this key in client-side code.**
   * **Header Format:** `Authorization: Bearer <SUPABASE_SERVICE_ROLE_KEY>`

### Request Format
* **Method:** `POST`
* **JSON Body:**
  ```json
  {
    "action": "sync_active",
    "version": "1.0.42",
    "is_mandatory": false,
    "update_url": "https://play.google.com/store/apps/details?id=com.honlor.app",
    "description": "Update notes/changelog text goes here..."
  }
  ```

### JavaScript / React Integration Example
You can copy this directly into your Admin Panel code:

```javascript
async function updateAppVersion(newVersionData, userSessionToken) {
  const url = "https://zloyckonnkiywllkyghf.supabase.co/functions/v1/manage-app-version";
  
  try {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${userSessionToken}` // User's access token
      },
      body: JSON.stringify({
        action: "sync_active",
        version: newVersionData.version,           // e.g. "1.0.42"
        is_mandatory: newVersionData.isMandatory,   // boolean
        update_url: newVersionData.updateUrl,       // string
        description: newVersionData.description     // string
      })
    });

    const result = await response.json();
    if (!response.ok || !result.success) {
      throw new Error(result.error || "Failed to update version target.");
    }
    
    console.log("Success:", result.message);
    return result;
  } catch (error) {
    console.error("Error syncing version:", error);
    throw error;
  }
}
```

* **Success Response (`200 OK`):**
  ```json
  {
    "success": true,
    "message": "Version 1.0.42 synced as active build target."
  }
  ```

* **Error Response (`401 Unauthorized`):**
  ```json
  {
    "success": false,
    "error": "new_row violates Row Level Security policy for table \"app_versions\""
  }
  ```
