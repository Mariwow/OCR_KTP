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
        Schema::create('passport_verifieds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id');

            $table->string('kode_negara', 3)->nullable();
            $table->string('no_paspor')->nullable();
            $table->string('nama')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('masa_berlaku')->nullable();
            $table->date('tanggal_terbentuk')->nullable();
            $table->string('no_reg')->nullable();

            $table->unsignedBigInteger('verified_by');
            $table->string('passport_image_path');

            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('submission_id')
                  ->references('id')
                  ->on('passports')
                  ->onDelete('cascade');

             $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passport_verifieds');
    }
};
