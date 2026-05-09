<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->text('abstract')->nullable()->after('project_type');
            $table->text('keywords')->nullable()->after('abstract');
            $table->string('pdf_path')->nullable()->after('pdf_file');
            $table->string('pdf_original_name')->nullable()->after('pdf_path');
            $table->string('pdf_mime')->nullable()->after('pdf_original_name');
            $table->unsignedBigInteger('pdf_size')->nullable()->after('pdf_mime');
            $table->string('file_hash', 64)->nullable()->unique()->after('pdf_size');
            $table->longText('pdf_text')->nullable()->after('file_hash');
            $table->foreignId('uploaded_by')->nullable()->after('pdf_text')->constrained('users')->nullOnDelete();
            $table->softDeletes();

            $table->index('title');
            $table->index('student_name');
            $table->index('supervisor');
            $table->index('project_type');
            $table->index('completion_year');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['student_name']);
            $table->dropIndex(['supervisor']);
            $table->dropIndex(['project_type']);
            $table->dropIndex(['completion_year']);
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropColumn([
                'abstract',
                'keywords',
                'pdf_path',
                'pdf_original_name',
                'pdf_mime',
                'pdf_size',
                'file_hash',
                'pdf_text',
                'deleted_at',
            ]);
        });
    }
};
