<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extension_sites', function (Blueprint $table) {
            $table->string('apps_manager_access_token')->nullable()->after('unsubscribed_at');
        });
    }

    public function down(): void
    {
        Schema::table('extension_sites', function (Blueprint $table) {
            $table->dropColumn('apps_manager_access_token');
        });
    }
};