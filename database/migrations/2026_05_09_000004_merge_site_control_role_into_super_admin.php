<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'site_control')
            ->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        //
    }
};
