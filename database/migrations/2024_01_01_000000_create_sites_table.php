<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) {
                $table->id();
                $table->enum('platform', ['shopify', 'bigcommerce', 'custom']);
                $table->string('name');
                $table->string('url');
                $table->timestamps();
                $table->timestamp('uninstalled_at')->nullable();
            });

            return;
        }

        Schema::table('sites', function (Blueprint $table) {
            if (! Schema::hasColumn('sites', 'platform')) {
                $table->enum('platform', ['shopify', 'bigcommerce', 'custom']);
            }
            if (! Schema::hasColumn('sites', 'name')) {
                $table->string('name');
            }
            if (! Schema::hasColumn('sites', 'url')) {
                $table->string('url');
            }
            if (! Schema::hasColumn('sites', 'created_at')) {
                $table->timestamps();
            }
            if (! Schema::hasColumn('sites', 'uninstalled_at')) {
                $table->timestamp('uninstalled_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};