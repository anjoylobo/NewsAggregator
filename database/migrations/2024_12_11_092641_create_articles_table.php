<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('url');
            $table->string('source');
            $table->string('category')->default('general')->nullable(false); 
            $table->datetime('published_at');
            $table->text('content')->nullable();
            $table->string('author')->nullable(); 
            $table->timestamps();

            $table->index(['source', 'published_at', 'category','author']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
