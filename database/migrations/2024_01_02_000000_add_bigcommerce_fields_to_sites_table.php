<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('store_hash')->nullable()->after('url');
            $table->text('access_token')->nullable()->after('store_hash');

            $table->unique(['platform', 'store_hash']);
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropUnique(['platform', 'store_hash']);
            $table->dropColumn(['store_hash', 'access_token']);
        });
    }
};
