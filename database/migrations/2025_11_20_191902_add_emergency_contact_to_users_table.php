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
        Schema::table('users', function (Blueprint $table) {
            $table->string('emergency_contact_first_name')->nullable()->after('health_notes');
            $table->string('emergency_contact_last_name')->nullable()->after('emergency_contact_first_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_last_name');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_relationship');
            $table->string('emergency_contact_email')->nullable()->after('emergency_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_first_name',
                'emergency_contact_last_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'emergency_contact_email',
            ]);
        });
    }
};
