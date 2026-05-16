<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('field_of_study')->nullable()->after('role');
            $table->text('interest_keywords')->nullable()->after('field_of_study');
            $table->json('preferred_categories')->nullable()->after('interest_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'field_of_study',
                'interest_keywords',
                'preferred_categories',
            ]);
        });
    }
};
