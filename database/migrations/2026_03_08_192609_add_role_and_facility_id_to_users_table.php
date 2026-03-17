<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user')->after('email');
        $table->unsignedBigInteger('facility_id')->nullable()->after('role');
        // $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('set null');
    });
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->dropColumn(['role', 'facility_id']);
        });
    }
};