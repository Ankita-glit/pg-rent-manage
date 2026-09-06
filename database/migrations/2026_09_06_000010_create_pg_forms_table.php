<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pg_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('template_file_path')->nullable(); // Optional Admin uploaded form template PDF/Doc
            $table->boolean('is_active')->default(true);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pg_forms');
    }
};
