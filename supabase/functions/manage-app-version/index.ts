/**
 * Edge Function: manage-app-version
 * ====================================
 * Called by the Honlor Admin Panel to sync the active app version to Supabase,
 * and by the mobile app to check if an update is required.
 *
 * Actions:
 *   POST { "action": "sync_active", "version": "v1.2.3", "is_mandatory": true, "update_url": "...", "description": "..." }
 *     → Upserts the active version record in the app_versions table.
 *     → Requires Service Role key (admin panel only, never expose to clients).
 *
 *   GET  ?action=get_active  (or POST { "action": "get_active" })
 *     → Returns the current active version. Used by the mobile app on launch.
 *     → Public — no auth required.
 *
 *   POST { "action": "check_version", "current_version": "v1.0.0" }
 *     → Returns whether the given version needs an update and if it's mandatory.
 *     → Public — no auth required.
 */

import { createClient } from "https://esm.sh/@supabase/supabase-js@2";

const SUPABASE_URL        = Deno.env.get("SUPABASE_URL")!;
const SUPABASE_ANON_KEY   = Deno.env.get("SUPABASE_ANON_KEY")!;
const SERVICE_ROLE_KEY    = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!;

const corsHeaders = {
  "Access-Control-Allow-Origin":  "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "GET, POST, OPTIONS",
};

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 204, headers: corsHeaders });
  }

  const url = new URL(req.url);

  // Resolve action from query string (GET) or JSON body (POST)
  let action        = url.searchParams.get("action") ?? null;
  let body: Record<string, unknown> = {};

  if (req.method === "POST") {
    try { body = await req.json(); } catch { /* empty body is fine */ }
    action = (body.action as string) ?? action;
  }

  // ── GET ACTIVE ───────────────────────────────────────────────────────────────
  if (action === "get_active" || (!action && req.method === "GET")) {
    const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
    const { data, error } = await supabase
      .from("app_versions")
      .select("version, is_mandatory, update_url, description, synced_at")
      .eq("id", 1)
      .single();

    if (error || !data) {
      return new Response(
        JSON.stringify({ success: false, error: error?.message ?? "No version data found." }),
        { status: 404, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    return new Response(
      JSON.stringify({ success: true, ...data }),
      { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }

  // ── CHECK VERSION ─────────────────────────────────────────────────────────────
  if (action === "check_version") {
    const currentVersion = body.current_version as string | undefined;
    if (!currentVersion) {
      return new Response(
        JSON.stringify({ success: false, error: "current_version is required." }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
    const { data, error } = await supabase
      .from("app_versions")
      .select("version, is_mandatory, update_url, description")
      .eq("id", 1)
      .single();

    if (error || !data) {
      return new Response(
        JSON.stringify({ success: false, error: "Could not fetch active version." }),
        { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    const needsUpdate = currentVersion !== data.version;

    return new Response(
      JSON.stringify({
        success:       true,
        needs_update:  needsUpdate,
        is_mandatory:  needsUpdate ? data.is_mandatory : false,
        latest_version: data.version,
        update_url:    needsUpdate ? data.update_url : null,
        description:   needsUpdate ? data.description : null,
      }),
      { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }

  // ── SYNC ACTIVE (admin panel → Supabase) ─────────────────────────────────────
  if (action === "sync_active") {
    // Verify caller is using the service role key
    const authHeader = req.headers.get("Authorization") ?? "";
    const callerKey  = authHeader.replace("Bearer ", "").trim();
    if (callerKey !== SERVICE_ROLE_KEY) {
      return new Response(
        JSON.stringify({ success: false, error: "Unauthorized." }),
        { status: 401, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    const version      = body.version      as string | undefined;
    const isMandatory  = body.is_mandatory  as boolean | undefined;
    const updateUrl    = body.update_url    as string | undefined;
    const description  = body.description  as string | undefined;

    if (!version || !updateUrl) {
      return new Response(
        JSON.stringify({ success: false, error: "version and update_url are required." }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Use service role client to bypass RLS
    const adminClient = createClient(SUPABASE_URL, SERVICE_ROLE_KEY);

    // We always upsert to id=1 (single-row active version record)
    const { error } = await adminClient
      .from("app_versions")
      .upsert({
        id:           1,
        version:      version,
        is_mandatory: isMandatory ?? false,
        update_url:   updateUrl,
        description:  description ?? "",
        synced_at:    new Date().toISOString(),
      }, { onConflict: "id" });

    if (error) {
      return new Response(
        JSON.stringify({ success: false, error: error.message }),
        { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    return new Response(
      JSON.stringify({ success: true, message: `Version ${version} synced as active build target.` }),
      { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }

  return new Response(
    JSON.stringify({ success: false, error: `Unknown action: ${action}. Valid: get_active, check_version, sync_active.` }),
    { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
  );
});
