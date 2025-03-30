<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id(); 
            $table->string('title'); 
            $table->string('slug')->unique();
            $table->string('author')->nullable(); 
            $table->string('short_desc')->nullable(); 
            $table->text('content')->nullable(); 
            $table->string('image')->nullable(); 
            $table->integer('status')->default(1); // 1 = Draft, 2 = Published, 3 = Archived, etc.
            $table->integer('view_count')->default(0); // To track the number of views
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
