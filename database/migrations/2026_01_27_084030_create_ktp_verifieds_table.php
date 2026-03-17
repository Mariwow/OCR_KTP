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
        Schema::create('ktp_verifieds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id'); 

            $table->string('nik', 20);
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir'); 
            $table->string('jenis_kelamin', 20);
            $table->string('alamat');
            $table->string('rt_rw', 10);
            $table->string('kel_desa');
            $table->string('kecamatan');
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('agama');
            $table->string('status_perkawinan');
            $table->string('pekerjaan');
            $table->string('kewarganegaraan');
            $table->string('berlaku_sampai')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();

            $table->string('ktp_image_path');
            
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('submission_id')
                  ->references('id')
                  ->on('read_ktp')
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
        Schema::dropIfExists('ktp_verified');
    }
};
