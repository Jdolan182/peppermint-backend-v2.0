<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmap_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 20)->default('#6366f1');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('roadmap_items', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('roadmap_categories')->nullOnDelete()->after('category');
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('roadmap_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->string('category')->nullable()->after('date');
        });

        Schema::dropIfExists('roadmap_categories');
    }
};
