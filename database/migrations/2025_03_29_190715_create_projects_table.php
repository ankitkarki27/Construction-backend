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
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); 
            $table->string('title'); 
            $table->string('slug')->unique(); 
            $table->string('short_desc')->nullable(); 
            $table->text('content')->nullable(); 
            // $table->enum('construction_type', ['residential', 'commercial', 'industrial', 'infrastructure', 'renovation','educational', 'transportation', 'others'])->nullable();   
            // $table->enum('sector', ['private', 'public', 'governmental'])->nullable();  
            $table->string('location')->nullable(); 
            $table->string('image')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps(); 
    
            $table->index(['slug', 'status']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
