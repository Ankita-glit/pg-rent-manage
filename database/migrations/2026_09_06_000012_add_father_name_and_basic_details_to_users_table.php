<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('name');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->text('permanent_address')->nullable()->after('id_proof_path');
            $table->string('college_office_name')->nullable()->after('permanent_address');
            $table->string('blood_group')->nullable()->after('college_office_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'father_name',
                'mother_name',
                'permanent_address',
                'college_office_name',
                'blood_group',
            ]);
        });
    }
};
