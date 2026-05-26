<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('plan')->nullable()->after('url');
            $table->boolean('is_trial')->default(false)->after('plan');
            $table->timestamp('trial_start_at')->nullable()->after('is_trial');
            $table->timestamp('trial_end_at')->nullable()->after('trial_start_at');
            $table->boolean('is_subscribed')->default(false)->after('trial_end_at');
            $table->timestamp('subscription_start_at')->nullable()->after('is_subscribed');
            $table->timestamp('subscription_end_at')->nullable()->after('subscription_start_at');
            $table->timestamp('unsubscribed_at')->nullable()->after('subscription_end_at');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'is_trial',
                'trial_start_at',
                'trial_end_at',
                'is_subscribed',
                'subscription_start_at',
                'subscription_end_at',
                'unsubscribed_at',
            ]);
        });
    }
};