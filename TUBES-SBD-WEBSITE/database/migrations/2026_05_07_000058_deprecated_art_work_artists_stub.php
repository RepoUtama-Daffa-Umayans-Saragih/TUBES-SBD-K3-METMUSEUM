<?php

use Illuminate\Database\Migrations\Migration;

// DEPRECATED: This table has been replaced by art_work_constituents (associative entity).
// This migration is retained as a no-op stub to preserve migration history ordering.
return new class extends Migration
{
    public function up(): void
    {
        // No-op: art_work_artists is deprecated. Attribution is handled by art_work_constituents.
    }

    public function down(): void
    {
        // No-op.
    }
};