-- ============================================================
-- SQL Migration: Allow Multi-Row App Version History
-- ============================================================
-- Run this directly in the Supabase SQL Editor:
-- Dashboard → SQL Editor → New Query → Paste → Run
-- ============================================================

-- 1. Drop the single_row check constraint if it exists
ALTER TABLE public.app_versions DROP CONSTRAINT IF EXISTS single_row;

-- 2. Align/sync the primary key id sequence to prevent duplicate key violations
SELECT setval(
    pg_get_serial_sequence('public.app_versions', 'id'), 
    coalesce(max(id), 1)
) FROM public.app_versions;
