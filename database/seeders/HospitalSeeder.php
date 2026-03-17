<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facility_id');
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            
            $table->string('patient_name')->nullable();
            $table->string('patient_phone')->nullable();
            $table->text('pickup_address')->nullable();
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);
            $table->text('notes')->nullable();
            
            $table->enum('status', ['pending', 'dispatched', 'en_route', 'arrived', 'completed', 'cancelled'])
                  ->default('pending');
            
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('en_route_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->unsignedBigInteger('dispatched_by')->nullable();
            $table->foreign('dispatched_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulance_requests');
    }
};