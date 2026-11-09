<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // created, updated, deactivated, activated, deleted, published
            $table->string('subject_type'); // category, announcement
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label'); // human-readable name/title
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
