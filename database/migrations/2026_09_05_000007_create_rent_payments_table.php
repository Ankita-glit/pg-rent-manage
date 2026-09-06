<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_invoice_id')->constrained('rent_invoices')->onDelete('cascade');
            $table->enum('payment_method', ['online', 'cash'])->default('online');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_time')->nullable(); // e.g. "14:30"
            $table->string('transaction_id')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->string('cash_receiver_name')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
    }
};
