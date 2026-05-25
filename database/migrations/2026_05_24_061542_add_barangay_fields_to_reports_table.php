<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('purok')->nullable(); // The specific area in the Barangay
            $table->dateTime('incident_date')->nullable();
            $table->string('complainant_contact')->nullable();
            $table->boolean('is_blotter')->default(false); // Official record status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
};
