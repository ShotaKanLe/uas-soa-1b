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
        // Tabel Code
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('code_type');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel staff mitra (harus dibuat pertama karena direferensikan banyak tabel)
        Schema::create('staff_mitras', function (Blueprint $table) {
            $table->id();
            $table->string('nama_staff');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('id_code')->constrained('codes');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel services (membutuhkan staff_mitra)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nama_service');
            $table->integer('durasi_service');
            $table->integer('harga_service');
            $table->string('deskripsi_service');
            $table->foreignId('id_staff_mitra')->constrained('staff_mitras');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel perusahaan (membutuhkan services)
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('kode_perusahaan')->unique();
            $table->string('email_perusahaan')->unique();
            $table->foreignId('id_service')->constrained('services');
            $table->date('tanggal_aktif_service');
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel staff perusahaan (membutuhkan perusahaan)
        Schema::create('staff_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_staff');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('id_perusahaan')->constrained('perusahaans');
            $table->foreignId('id_code')->constrained('codes');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel bahan bakar (tidak ada ketergantungan)
        Schema::create('bahan_bakars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan_bakar');
            $table->string('jenis_bahan_bakar');
            $table->decimal('co2perliter', 8, 4);
            $table->decimal('ch4perliter', 8, 4);
            $table->decimal('n2Operliter', 8, 4);
            $table->decimal('Co2eperliter', 8, 4);
            $table->decimal('WTTperliter', 8, 4);
            $table->decimal('rerata_konsumsi_literperkm', 8, 4);
            $table->integer('harga_bahan_bakar_per_liter');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel karyawan perusahaan (membutuhkan perusahaan)
        Schema::create('karyawan_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perusahaan')->constrained('perusahaans');
            $table->string('nama_karyawan');
            $table->string('jabatan');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel transportasi (tidak ada ketergantungan)
        Schema::create('transportasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_transportasi');
            $table->string('jenis_transportasi');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel alamat rumah (membutuhkan karyawan)
        Schema::create('alamat_rumahs', function (Blueprint $table) {
            $table->id();
            $table->string('alamat_rumah');
            $table->foreignId('id_karyawan')->constrained('karyawan_perusahaans');
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel informasi (membutuhkan staff mitra)
        Schema::create('informasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul_informasi');
            $table->foreignId('id_staff_mitra')->constrained('staff_mitras');
            $table->text('isi_informasi');
            $table->string('gambar_informasi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel hasil analisis emisi (membutuhkan perusahaan)
        Schema::create('hasil_analisis_emisis', function (Blueprint $table) {
            $table->id();
            $table->text('nama_analisis');
            $table->foreignId('id_perusahaan')->constrained('perusahaans');
            $table->date('tanggal_analisis');
            $table->text('file_pdf');
            $table->integer('total_data')->nullable();
            $table->decimal('total_emisi_karbon', 10, 2)->nullable();
            $table->decimal('rata_rata_emisi', 10, 2)->nullable();
            $table->json('filter_applied')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel perjalanan karyawan (membutuhkan banyak relasi)
        Schema::create('perjalanan_karyawan_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('karyawan_perusahaans');
            $table->foreignId('id_transportasi')->constrained('transportasis');
            $table->foreignId('id_bahan_bakar')->constrained('bahan_bakars');
            $table->foreignId('id_perusahaan')->constrained('perusahaans');
            $table->foreignId('id_alamat')->constrained('alamat_rumahs');
            $table->date('tanggal_perjalanan');
            $table->integer('jarak_perjalanan');
            $table->decimal('total_co2', 8, 4);
            $table->decimal('total_ch4', 8, 4);
            $table->decimal('total_n2O', 8, 4);
            $table->decimal('total_co2e', 8, 4);
            $table->decimal('total_WTT', 8, 4);
            $table->decimal('total_emisi_karbon', 8, 4);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel pivot perjalanan (many-to-many)
        Schema::create('perjalanans', function (Blueprint $table) {
            $table->foreignId('id_hasil_analisis')->constrained('hasil_analisis_emisis');
            $table->foreignId('id_perjalanan')->constrained('perjalanan_karyawan_perusahaans');
            $table->primary(['id_hasil_analisis', 'id_perjalanan']);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel hasil konsultasi (membutuhkan beberapa relasi)
        Schema::create('hasil_konsultasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perusahaan')->constrained('perusahaans');
            $table->text('nama_konsultasi');
            $table->date('tanggal_konsultasi');
            $table->text('isi_konsultasi');
            $table->string('status_konsultasi')->default('OPEN');
            $table->foreignId('id_hasil_analisis')->constrained('hasil_analisis_emisis');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_staff')->constrained('staff_mitras');
            $table->foreignId('id_konsultasi')->constrained('hasil_konsultasis');
            $table->text('judul_pesan');
            $table->text('isi_pesan');
            $table->text('file_pdf')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::create('transactions', function (Blueprint $table) {
        //     $table->id();
        //     $table->integer('user_id');
        //     $table->integer('product_id');
        //     $table->integer('price');
        //     $table->enum('status', ['pending', 'success', 'failed']);
        //     $table->string('snap_token')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Urutan penghapusan harus kebalikan dari pembuatan
        // Schema::dropIfExists('pending_companies');
        // Schema::dropIfExists('transactions');
        Schema::dropIfExists('pesans');
        Schema::dropIfExists('hasil_konsultasis');
        Schema::dropIfExists('perjalanans');
        Schema::dropIfExists('perjalanan_karyawan_perusahaans');
        Schema::dropIfExists('hasil_analisis_emisis');
        Schema::dropIfExists('informasis');
        Schema::dropIfExists('alamat_rumahs');
        Schema::dropIfExists('transportasis');
        Schema::dropIfExists('karyawan_perusahaans');
        Schema::dropIfExists('bahan_bakars');
        Schema::dropIfExists('staff_perusahaans');
        Schema::dropIfExists('perusahaans');
        Schema::dropIfExists('services');
        Schema::dropIfExists('staff_mitras');
        Schema::dropIfExists('codes');
    }
};
