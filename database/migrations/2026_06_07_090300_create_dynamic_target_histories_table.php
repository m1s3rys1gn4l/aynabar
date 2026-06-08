<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_target_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('code_id')->constrained('codes')->cascadeOnDelete();
            $table->text('previous_target_url')->nullable();
            $table->text('new_target_url');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->index('code_id');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_target_histories');
    }
};
