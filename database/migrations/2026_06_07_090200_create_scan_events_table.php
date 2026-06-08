<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('code_id')->constrained('codes')->cascadeOnDelete();
            $table->timestamp('scanned_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->timestamps();

            $table->index('code_id');
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_events');
    }
};
