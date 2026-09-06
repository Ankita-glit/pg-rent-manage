<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation'); // 'Caretaker', 'Warden', 'Maintenance Supervisor', 'Cleaning Staff', 'Security Guard'
            $table->string('phone');
            $table->string('email')->nullable();
            $table->decimal('monthly_salary', 10, 2);
            $table->date('joining_date');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
