<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('renter'); // 'admin', 'renter'
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('id_type')->nullable(); // 'Aadhaar', 'PAN', 'Passport', 'Driving License'
            $table->string('id_number')->nullable();
            $table->string('id_proof_path')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->foreignId('assigned_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->string('bed_number')->nullable();
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->integer('rent_due_day')->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_room_id']);
            $table->dropColumn([
                'role', 'phone', 'emergency_contact', 'id_type', 'id_number',
                'id_proof_path', 'joining_date', 'status', 'assigned_room_id',
                'bed_number', 'monthly_rent', 'security_deposit', 'rent_due_day'
            ]);
        });
    }
};
