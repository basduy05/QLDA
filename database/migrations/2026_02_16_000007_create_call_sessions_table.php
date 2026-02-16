<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('callee_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('ringing');
            $table->longText('offer_sdp')->nullable();
            $table->longText('answer_sdp')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['caller_id', 'callee_id']);
            $table->index(['callee_id', 'status']);
            $table->index(['caller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};
