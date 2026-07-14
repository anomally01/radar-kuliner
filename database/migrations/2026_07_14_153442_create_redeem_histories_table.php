<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redeem_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('voucher_type'); // e.g. "Paket Data 1GB", "Google Play Rp50.000"
            $table->string('voucher_code');
            $table->integer('points_used');
            $table->enum('status', ['success', 'pending', 'failed'])->default('success');
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redeem_histories');
    }
};
