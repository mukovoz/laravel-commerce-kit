<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extension_sites', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['shopify', 'bigcommerce', 'custom']);
            $table->string('name');
            $table->string('url');
            $table->timestamps();
            $table->timestamp('uninstalled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extension_sites');
    }
};
