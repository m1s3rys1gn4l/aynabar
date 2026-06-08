<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('label');
            $table->string('kind');
            $table->string('mode');
            $table->string('barcode_format')->nullable()->default('code128');
            $table->text('static_payload')->nullable();
            $table->string('dynamic_slug')->nullable()->unique();
            $table->text('dynamic_target_url')->nullable();
            $table->timestamps();

            $table->index('owner_id');
            $table->index('dynamic_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
