<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            
            $table->integer('icu_beds_total')->default(0);
            $table->integer('icu_beds_available')->default(0);
            $table->integer('general_beds_total')->default(0);
            $table->integer('general_beds_available')->default(0);
            $table->integer('maternity_beds_total')->default(0);
            $table->integer('maternity_beds_available')->default(0);
            $table->integer('pediatric_beds_total')->default(0);
            $table->integer('pediatric_beds_available')->default(0);
            
            $table->integer('doctors_on_duty')->default(0);
            $table->integer('nurses_on_duty')->default(0);
            $table->integer('paramedics_on_duty')->default(0);
            
            $table->boolean('ventilators_available')->default(false);
            $table->integer('ventilator_count')->default(0);
            
            $table->boolean('ambulances_available')->default(false);
            $table->integer('ambulance_count')->default(0);
            
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_resources');
    }
};