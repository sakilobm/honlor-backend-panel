/**
 * Edge Function: request-account-deletion
 * ========================================
 * Handles user data deletion requests for GDPR/App Store compliance.
 * 
 * Public endpoint for users to submit deletion requests,
 * and service-role-protected endpoints for admin dashboard operations.
 *
 * Actions:
 *   POST { "action": "submit_request", "email": "user@example.com", "reason": "...", "full_name": "..." }
 *     → Public. Inserts a new deletion request into the deletion_requests table.
 *     → Validates email format, enforces 24h duplicate prevention.
 *
 *   POST { "action": "list_requests" }
 *     → Service Role only. Returns all deletion requests for admin dashboard.
 *
 *   POST { "action": "update_request", "id": "uuid", "status": "approved"|"rejected", "admin_note": "..." }
 *     → Service Role only. Updates request status with optional admin note.
 */

import { createClient } from "https://esm.sh/@supabase/supabase-js@2";

const SUPABASE_URL      = Deno.env.get("SUPABASE_URL")!;
const SUPABASE_ANON_KEY = Deno.env.get("SUPABASE_ANON_KEY")!;
const SERVICE_ROLE_KEY  = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!;

const corsHeaders = {
  "Access-Control-Allow-Origin":  "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

/**
 * Validates an email address format using a strict regex pattern.
 * Does not verify deliverability — only structural correctness.
 */
function isValidEmail(email: string): boolean {
  if (!email || typeof email !== "string") return false;
  if (email.length > 255) return false;
  const pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  return pattern.test(email);
}

/**
 * Sanitizes a text string by trimming whitespace and enforcing a max length.
 * Returns null if the input is empty after trimming.
 */
function sanitizeText(input: unknown, maxLength: number): string | null {
  if (input === null || input === undefined) return null;
  if (typeof input !== "string") return null;
  const trimmed = input.trim();
  if (trimmed.length === 0) return null;
  return trimmed.substring(0, maxLength);
}

/**
 * Returns a JSON error response with consistent structure.
 */
function errorResponse(message: string, status: number): Response {
  return new Response(
    JSON.stringify({ success: false, error: message }),
    { status, headers: { ...corsHeaders, "Content-Type": "application/json" } }
  );
}

/**
 * Returns a JSON success response with consistent structure.
 */
function successResponse(data: Record<string, unknown>, status = 200): Response {
  return new Response(
    JSON.stringify({ success: true, ...data }),
    { status, headers: { ...corsHeaders, "Content-Type": "application/json" } }
  );
}

/**
 * Verifies that the request carries a Service Role JWT in the Authorization header.
 * Decodes the JWT payload (base64) and checks that the role claim is 'service_role'.
 */
function isServiceRole(req: Request): boolean {
  const authHeader = req.headers.get("Authorization") ?? "";
  const token = authHeader.replace("Bearer ", "").trim();
  if (!token) return false;

  try {
    // JWT structure: header.payload.signature — decode the payload (middle part)
    const parts = token.split(".");
    if (parts.length !== 3) return false;

    // Decode base64url payload
    const payloadB64 = parts[1].replace(/-/g, "+").replace(/_/g, "/");
    const padLength = (4 - (payloadB64.length % 4)) % 4;
    const padded = payloadB64 + "=".repeat(padLength);
    const payloadJson = atob(padded);
    const payload = JSON.parse(payloadJson);

    return payload.role === "service_role";
  } catch {
    return false;
  }
}

Deno.serve(async (req: Request) => {
  // ── CORS Preflight ──────────────────────────────────────────────────────────
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 204, headers: corsHeaders });
  }

  // Only accept POST
  if (req.method !== "POST") {
    return errorResponse("Method not allowed. Use POST.", 405);
  }

  // Parse JSON body
  let body: Record<string, unknown> = {};
  try {
    body = await req.json();
  } catch {
    return errorResponse("Invalid JSON body.", 400);
  }

  const action = body.action as string | undefined;
  if (!action) {
    return errorResponse("Missing 'action' field. Valid: submit_request, list_requests, update_request.", 400);
  }

  // ── SUBMIT REQUEST (Public) ─────────────────────────────────────────────────
  if (action === "submit_request") {
    const email    = sanitizeText(body.email, 255);
    const reason   = sanitizeText(body.reason, 1000);
    const fullName = sanitizeText(body.full_name, 100);

    if (!email || !isValidEmail(email)) {
      return errorResponse("A valid email address is required.", 400);
    }

    // Use service role client to bypass RLS (table has no public policies)
    const adminClient = createClient(SUPABASE_URL, SERVICE_ROLE_KEY);

    // Duplicate prevention: check for existing pending request from same email within 24h
    const twentyFourHoursAgo = new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString();
    const { data: existing, error: checkError } = await adminClient
      .from("deletion_requests")
      .select("id")
      .eq("email", email)
      .eq("status", "pending")
      .gte("created_at", twentyFourHoursAgo)
      .limit(1);

    if (checkError) {
      return errorResponse("Failed to process request. Please try again later.", 500);
    }

    if (existing && existing.length > 0) {
      return errorResponse(
        "A deletion request for this email is already being processed. Please allow up to 30 days for review.",
        409
      );
    }

    // Insert the new deletion request
    const { error: insertError } = await adminClient
      .from("deletion_requests")
      .insert({
        email:     email,
        full_name: fullName,
        reason:    reason,
        status:    "pending",
      });

    if (insertError) {
      return errorResponse("Failed to submit deletion request. Please try again later.", 500);
    }

    return successResponse({
      message: "Your account deletion request has been submitted successfully. Our team will review it within 30 days.",
    });
  }

  // ── LIST REQUESTS (Service Role Only) ───────────────────────────────────────
  if (action === "list_requests") {
    if (!isServiceRole(req)) {
      return errorResponse("Unauthorized.", 401);
    }

    const adminClient = createClient(SUPABASE_URL, SERVICE_ROLE_KEY);

    const { data, error } = await adminClient
      .from("deletion_requests")
      .select("*")
      .order("created_at", { ascending: false });

    if (error) {
      return errorResponse("Failed to fetch deletion requests.", 500);
    }

    // Compute metrics
    const requests = data || [];
    let pending  = 0;
    let approved = 0;
    let rejected = 0;

    for (const req of requests) {
      if (req.status === "pending")  pending++;
      if (req.status === "approved") approved++;
      if (req.status === "rejected") rejected++;
    }

    const velocity = requests.length > 0
      ? Math.round(((approved + rejected) / requests.length) * 1000) / 10
      : 0;

    return successResponse({
      requests,
      stats: {
        total:    requests.length,
        pending,
        approved,
        rejected,
        velocity,
      },
    });
  }

  // ── UPDATE REQUEST (Service Role Only) ──────────────────────────────────────
  if (action === "update_request") {
    if (!isServiceRole(req)) {
      return errorResponse("Unauthorized.", 401);
    }

    const id        = body.id as string | undefined;
    const status    = body.status as string | undefined;
    const adminNote = sanitizeText(body.admin_note, 500);

    if (!id || typeof id !== "string") {
      return errorResponse("Missing or invalid 'id' field.", 400);
    }

    if (!status || !["approved", "rejected"].includes(status)) {
      return errorResponse("Invalid 'status'. Must be 'approved' or 'rejected'.", 400);
    }

    const adminClient = createClient(SUPABASE_URL, SERVICE_ROLE_KEY);

    const updatePayload: Record<string, unknown> = {
      status,
      reviewed_at: new Date().toISOString(),
      admin_note:  adminNote,
    };

    // If a reviewed_by identifier is passed, include it
    if (body.reviewed_by && typeof body.reviewed_by === "string") {
      updatePayload.reviewed_by = sanitizeText(body.reviewed_by, 100);
    }

    const { error } = await adminClient
      .from("deletion_requests")
      .update(updatePayload)
      .eq("id", id);

    if (error) {
      return errorResponse("Failed to update deletion request.", 500);
    }

    return successResponse({
      message: `Deletion request ${status} successfully.`,
    });
  }

  // ── Unknown Action ──────────────────────────────────────────────────────────
  return errorResponse(
    `Unknown action: '${action}'. Valid actions: submit_request, list_requests, update_request.`,
    400
  );
});
