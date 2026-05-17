<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('pdf_storage_disk')->nullable()->after('pdf_path');
            $table->string('pdf_storage_key')->nullable()->after('pdf_storage_disk');
            $table->text('pdf_url')->nullable()->after('pdf_storage_key');
            $table->text('pdf_download_url')->nullable()->after('pdf_url');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'pdf_storage_disk',
                'pdf_storage_key',
                'pdf_url',
                'pdf_download_url',
            ]);
        });
    }
};
