<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * No changes needed - standardized on 'success' status throughout the system
     * All payment queries now use status='success'
     */
    public function up(): void
    {
        // No database changes needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse
    }
};
