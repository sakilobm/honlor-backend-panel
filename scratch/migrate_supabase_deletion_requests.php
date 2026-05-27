<?php
/**
 * Migration Script: Supabase Deletion Requests Table
 * ===================================================
 * Creates the `deletion_requests` table in Supabase PostgreSQL
 * for storing user account deletion requests submitted via the
 * public data-deletion page.
 *
 * Run this SQL directly in the Supabase SQL Editor:
 *   Dashboard → SQL Editor → New Query → Paste → Run
 *
 * Table is protected by RLS with NO public policies,
 * meaning only the service role key can read/write.
 */

$sql = <<<SQL

-- ============================================================
-- Table: public.deletion_requests
-- Purpose: Stores user-submitted account deletion requests
-- Access: Service Role only (RLS enabled, no public policies)
-- ============================================================

CREATE TABLE IF NOT EXISTS public.deletion_requests (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    email TEXT NOT NULL,
    full_name TEXT,
    reason TEXT,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
    admin_note TEXT,
    created_at TIMESTAMPTZ DEFAULT now(),
    reviewed_at TIMESTAMPTZ,
    reviewed_by TEXT
);

-- Enable Row Level Security (deny all public access by default)
ALTER TABLE public.deletion_requests ENABLE ROW LEVEL SECURITY;

-- Index on email for fast duplicate lookups
CREATE INDEX IF NOT EXISTS idx_deletion_requests_email 
    ON public.deletion_requests (email);

-- Index on status for admin dashboard filtering
CREATE INDEX IF NOT EXISTS idx_deletion_requests_status 
    ON public.deletion_requests (status);

-- Index on created_at for chronological sorting
CREATE INDEX IF NOT EXISTS idx_deletion_requests_created_at 
    ON public.deletion_requests (created_at DESC);

SQL;

echo "=== Supabase Deletion Requests Table Migration ===\n\n";
echo "Copy and paste the following SQL into the Supabase SQL Editor:\n";
echo "Dashboard → SQL Editor → New Query → Paste → Run\n\n";
echo str_repeat("─", 60) . "\n\n";
echo $sql;
echo "\n" . str_repeat("─", 60) . "\n\n";
echo "After running, verify:\n";
echo "  1. Table 'deletion_requests' exists in Table Editor\n";
echo "  2. RLS is enabled (lock icon visible)\n";
echo "  3. No public policies exist (service role only)\n";
