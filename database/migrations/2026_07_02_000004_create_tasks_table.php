<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('type_id')->constrained('task_types');
            $table->foreignId('status_id')->constrained('task_statuses');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->date('due_date')->nullable();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consumer_id')->nullable()->constrained('consumers')->nullOnDelete();
            $table->foreignId('roadmap_item_id')->nullable()->constrained('roadmap_items')->nullOnDelete();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_consumer_id')->nullable()->constrained('consumers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
