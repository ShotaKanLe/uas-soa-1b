<?php

namespace Database\Seeders;

use App\Models\AlamatRumah;
use App\Models\BahanBakar;
use App\Models\Code;
use App\Models\HasilAnalisisEmisi;
use App\Models\HasilKonsultasi;
use App\Models\Informasi;
use App\Models\KaryawanPerusahaan;
use App\Models\Perjalanan;
use App\Models\PerjalananKaryawanPerusahaan;
use App\Models\Perusahaan;
use App\Models\Pesan;
use App\Models\Service;
use App\Models\StaffMitra;
use App\Models\staffPerusahaan;
use App\Models\Transportasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data Code
        Code::create([
            'id' => '1',
            'code' => 'SM2024001',
            'code_type' => 'staff_mitra',
            'status' => 'aktif',
        ]);

        Code::create([
            'id' => '2',
            'code' => 'SP2024001',
            'code_type' => 'staff_perusahaan',
            'status' => 'aktif',
        ]);

        Code::create([
            'id' => '3',
            'code' => 'SM2024002',
            'code_type' => 'staff_mitra',
            'status' => 'aktif',
        ]);

        Code::create([
            'id' => '4',
            'code' => 'SP2024002',
            'code_type' => 'staff_perusahaan',
            'status' => 'tidak_aktif',
        ]);

        // Data Staff Mitra
        StaffMitra::create([
            'id' => '1',
            'nama_staff' => 'Dr. Ahmad Rizki',
            'password' => Hash::make('admin'),
            'email' => 'admin@example.com',
            'id_code' => '1',
        ]);

        StaffMitra::create([
            'id' => '2',
            'nama_staff' => 'Siti Nurhaliza, M.Sc',
            'password' => Hash::make('staff456'),
            'email' => 'siti.nurhaliza@carbonmitra.com',
            'id_code' => '3',
        ]);

        // Data Service
        Service::create([
            'id' => '1',
            'id_staff_mitra' => '1',
            'nama_service' => 'Carbon Footprint Assessment',
            'durasi_service' => '60',
            'harga_service' => '2500000',
            'deskripsi_service' => 'Layanan analisis jejak karbon perusahaan dengan metodologi GHG Protocol',
        ]);

        Service::create([
            'id' => '2',
            'id_staff_mitra' => '2',
            'nama_service' => 'Sustainability Consulting',
            'durasi_service' => '90',
            'harga_service' => '3750000',
            'deskripsi_service' => 'Konsultasi keberlanjutan dan strategi pengurangan emisi karbon',
        ]);

        Service::create([
            'id' => '3',
            'id_staff_mitra' => '1',
            'nama_service' => 'Environmental Impact Monitoring',
            'durasi_service' => '45',
            'harga_service' => '1800000',
            'deskripsi_service' => 'Monitoring dan pelaporan dampak lingkungan secara berkala',
        ]);

        // Data Perusahaan
        Perusahaan::create([
            'id' => '1',
            'id_service' => '1',
            'nama_perusahaan' => 'PT. Teknologi Hijau Indonesia',
            'email_perusahaan' => 'info@teknologihijau.co.id',
            'kode_perusahaan' => 'THI2024001',
            'tanggal_aktif_service' => '2024-01-15',
            'latitude' => '-0.9374172374330967',
            'longitude' => '100.38717882145211',
        ]);

        Perusahaan::create([
            'id' => '2',
            'id_service' => '2',
            'nama_perusahaan' => 'PT. Industri Berkelanjutan Nusantara',
            'email_perusahaan' => 'contact@industriberkelajutan.com',
            'kode_perusahaan' => 'IBN2024002',
            'tanggal_aktif_service' => '2024-02-20',
            'latitude' => '-0.9450000000000000',
            'longitude' => '100.39000000000000',
        ]);

        Perusahaan::create([
            'id' => '3',
            'id_service' => '3',
            'nama_perusahaan' => 'CV. Energi Terbarukan Sumatra',
            'email_perusahaan' => 'admin@energiterbarukan.id',
            'kode_perusahaan' => 'ETS2024003',
            'tanggal_aktif_service' => '2024-03-10',
            'latitude' => '-0.9300000000000000',
            'longitude' => '100.38000000000000',
        ]);

        // Data Staff Perusahaan
        staffPerusahaan::create([
            'id' => '1',
            'nama_staff' => 'Budi Santoso',
            'email' => 'staff@example.com',
            'password' => Hash::make('staff'),
            'id_perusahaan' => '1',
            'id_code' => '2',
        ]);

        staffPerusahaan::create([
            'id' => '2',
            'nama_staff' => 'Maya Sari',
            'email' => 'maya.sari@industriberkelajutan.com',
            'password' => Hash::make('maya456'),
            'id_perusahaan' => '2',
            'id_code' => '4',
        ]);

        // Data Bahan Bakar
        BahanBakar::create([
            'id' => '1',
            'nama_bahan_bakar' => 'Pertalite',
            'harga_bahan_bakar_per_liter' => '10000',
            'jenis_bahan_bakar' => 'Bensin',
            'co2perliter' => 2.35,
            'ch4perliter' => 0.00012,
            'n2Operliter' => 0.0106,
            'Co2eperliter' => 0.00236,
            'WTTperliter' => 0.00045,
            'rerata_konsumsi_literperkm' => 0.06,
        ]);

        BahanBakar::create([
            'id' => '2',
            'nama_bahan_bakar' => 'Pertamax',
            'harga_bahan_bakar_per_liter' => '12800',
            'jenis_bahan_bakar' => 'Bensin',
            'co2perliter' => 2.31,
            'ch4perliter' => 0.00010,
            'n2Operliter' => 0.0102,
            'Co2eperliter' => 0.00232,
            'WTTperliter' => 0.00042,
            'rerata_konsumsi_literperkm' => 0.055,
        ]);

        BahanBakar::create([
            'id' => '3',
            'nama_bahan_bakar' => 'Solar',
            'harga_bahan_bakar_per_liter' => '6800',
            'jenis_bahan_bakar' => 'Diesel',
            'co2perliter' => 2.67,
            'ch4perliter' => 0.00008,
            'n2Operliter' => 0.0095,
            'Co2eperliter' => 0.00270,
            'WTTperliter' => 0.00050,
            'rerata_konsumsi_literperkm' => 0.045,
        ]);

        // Data Karyawan Perusahaan
        KaryawanPerusahaan::create([
            'id' => '1',
            'id_perusahaan' => '1',
            'nama_karyawan' => 'Andi Pratama',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('karyawan'),
            'jabatan' => 'Environmental Engineer',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-05-15',
        ]);

        KaryawanPerusahaan::create([
            'id' => '2',
            'id_perusahaan' => '1',
            'nama_karyawan' => 'Dewi Lestari',
            'email' => 'dewi.lestari@teknologihijau.co.id',
            'password' => Hash::make('dewi456'),
            'jabatan' => 'Sustainability Manager',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1988-12-22',
        ]);

        KaryawanPerusahaan::create([
            'id' => '3',
            'id_perusahaan' => '2',
            'nama_karyawan' => 'Rudi Hartono',
            'email' => 'rudi.hartono@industriberkelajutan.com',
            'password' => Hash::make('rudi789'),
            'jabatan' => 'Carbon Analyst',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1992-08-10',
        ]);

        // Data Transportasi
        Transportasi::create([
            'id' => '1',
            'nama_transportasi' => 'Sepeda Motor',
            'jenis_transportasi' => 'Kendaraan Pribadi',
        ]);

        Transportasi::create([
            'id' => '2',
            'nama_transportasi' => 'Mobil Pribadi',
            'jenis_transportasi' => 'Kendaraan Pribadi',
        ]);

        Transportasi::create([
            'id' => '3',
            'nama_transportasi' => 'Angkutan Umum',
            'jenis_transportasi' => 'Transportasi Publik',
        ]);

        // Data Alamat Rumah
        AlamatRumah::create([
            'id' => '1',
            'id_karyawan' => '1',
            'alamat_rumah' => 'Jl. Raya Padang No. 123, Kuranji, Padang',
            'latitude' => '-0.9444594235485032',
            'longitude' => '100.39095471478592',
        ]);

        AlamatRumah::create([
            'id' => '2',
            'id_karyawan' => '2',
            'alamat_rumah' => 'Jl. Sudirman No. 456, Lubuk Begalung, Padang',
            'latitude' => '-0.9500000000000000',
            'longitude' => '100.38500000000000',
        ]);

        AlamatRumah::create([
            'id' => '3',
            'id_karyawan' => '3',
            'alamat_rumah' => 'Jl. Ahmad Yani No. 789, Nanggalo, Padang',
            'latitude' => '-0.9400000000000000',
            'longitude' => '100.39500000000000',
        ]);

        // Data Informasi
        Informasi::create([
            'id' => '1',
            'judul_informasi' => 'Tips Mengurangi Jejak Karbon di Tempat Kerja',
            'id_staff_mitra' => '1',
            'isi_informasi' => 'Artikel ini membahas berbagai strategi praktis untuk mengurangi emisi karbon di lingkungan kerja, termasuk efisiensi energi, penggunaan transportasi ramah lingkungan, dan pengelolaan limbah yang lebih baik.',
            'gambar_informasi' => 'tips_karbon_kerja.jpg',
        ]);

        Informasi::create([
            'id' => '2',
            'judul_informasi' => 'Pentingnya Monitoring Emisi Karbon untuk Perusahaan',
            'id_staff_mitra' => '2',
            'isi_informasi' => 'Mengapa perusahaan perlu melakukan monitoring emisi karbon secara berkala dan bagaimana hal ini dapat memberikan manfaat jangka panjang bagi keberlanjutan bisnis.',
            'gambar_informasi' => 'monitoring_emisi.jpg',
        ]);

        Informasi::create([
            'id' => '3',
            'judul_informasi' => 'Tren Teknologi Hijau 2024',
            'id_staff_mitra' => '1',
            'isi_informasi' => 'Eksplorasi teknologi terbaru yang dapat membantu perusahaan mencapai target net-zero emission, termasuk renewable energy, carbon capture, dan smart grid technology.',
            'gambar_informasi' => 'teknologi_hijau_2024.jpg',
        ]);

        // Data Hasil Analisis Emisi
        HasilAnalisisEmisi::create([
            'id' => '1',
            'nama_analisis' => 'Analisis Jejak Karbon Q1 2024 - PT. Teknologi Hijau Indonesia',
            'id_perusahaan' => '1',
            'tanggal_analisis' => '2024-04-15',
            'file_pdf' => 'analisis_q1_2024_thi.pdf',
        ]);

        HasilAnalisisEmisi::create([
            'id' => '2',
            'nama_analisis' => 'Carbon Footprint Assessment - PT. Industri Berkelanjutan Nusantara',
            'id_perusahaan' => '2',
            'tanggal_analisis' => '2024-05-20',
            'file_pdf' => 'carbon_assessment_ibn.pdf',
        ]);

        HasilAnalisisEmisi::create([
            'id' => '3',
            'nama_analisis' => 'Laporan Emisi Tahunan 2023 - CV. Energi Terbarukan Sumatra',
            'id_perusahaan' => '3',
            'tanggal_analisis' => '2024-03-30',
            'file_pdf' => 'laporan_emisi_2023_ets.pdf',
        ]);

        // Data Perjalanan Karyawan Perusahaan
        PerjalananKaryawanPerusahaan::create([
            'id' => '1',
            'id_karyawan' => '1',
            'id_transportasi' => '1',
            'id_bahan_bakar' => '1',
            'id_perusahaan' => '1',
            'id_alamat' => '1',
            'tanggal_perjalanan' => '2024-04-15',
            'total_co2' => '4.7',
            'total_ch4' => '0.00024',
            'total_n2O' => '0.0212',
            'total_co2e' => '4.72',
            'total_WTT' => '0.0009',
            'jarak_perjalanan' => '20',
            'total_emisi_karbon' => '4.72',
        ]);

        PerjalananKaryawanPerusahaan::create([
            'id' => '2',
            'id_karyawan' => '2',
            'id_transportasi' => '2',
            'id_bahan_bakar' => '2',
            'id_perusahaan' => '1',
            'id_alamat' => '2',
            'tanggal_perjalanan' => '2024-04-15',
            'total_co2' => '6.16',
            'total_ch4' => '0.00027',
            'total_n2O' => '0.0272',
            'total_co2e' => '6.19',
            'total_WTT' => '0.00112',
            'jarak_perjalanan' => '15',
            'total_emisi_karbon' => '6.19',
        ]);

        PerjalananKaryawanPerusahaan::create([
            'id' => '3',
            'id_karyawan' => '3',
            'id_transportasi' => '3',
            'id_bahan_bakar' => '3',
            'id_perusahaan' => '2',
            'id_alamat' => '3',
            'tanggal_perjalanan' => '2024-04-16',
            'total_co2' => '2.14',
            'total_ch4' => '0.00006',
            'total_n2O' => '0.0076',
            'total_co2e' => '2.16',
            'total_WTT' => '0.0004',
            'jarak_perjalanan' => '8',
            'total_emisi_karbon' => '2.16',
        ]);

        // Data Perjalanan
        Perjalanan::create([
            'id_hasil_analisis' => '1',
            'id_perjalanan' => '1',
        ]);

        Perjalanan::create([
            'id_hasil_analisis' => '1',
            'id_perjalanan' => '2',
        ]);

        Perjalanan::create([
            'id_hasil_analisis' => '2',
            'id_perjalanan' => '3',
        ]);

        // Data Hasil Konsultasi
        HasilKonsultasi::create([
            'id' => '1',
            'id_perusahaan' => '1',
            'nama_konsultasi' => 'Strategi Pengurangan Emisi Kendaraan Operasional',
            'tanggal_konsultasi' => '2024-04-20',
            'isi_konsultasi' => 'Rekomendasi implementasi kendaraan listrik untuk armada perusahaan dan optimasi rute perjalanan untuk mengurangi jejak karbon.',
            'status_konsultasi' => 'OPEN',
            'id_hasil_analisis' => '1',
        ]);

        HasilKonsultasi::create([
            'id' => '2',
            'id_perusahaan' => '2',
            'nama_konsultasi' => 'Audit Energi dan Efisiensi Operasional',
            'tanggal_konsultasi' => '2024-05-25',
            'isi_konsultasi' => 'Evaluasi penggunaan energi di fasilitas produksi dan rekomendasi untuk meningkatkan efisiensi energi serta mengurangi konsumsi listrik.',
            'status_konsultasi' => 'OPEN',
            'id_hasil_analisis' => '2',
        ]);

        HasilKonsultasi::create([
            'id' => '3',
            'id_perusahaan' => '3',
            'nama_konsultasi' => 'Implementasi Sistem Monitoring Emisi Real-time',
            'tanggal_konsultasi' => '2024-04-10',
            'isi_konsultasi' => 'Panduan implementasi sistem monitoring emisi secara real-time untuk meningkatkan akurasi pelaporan dan memudahkan pengambilan keputusan operasional.',
            'status_konsultasi' => 'CLOSED',
            'id_hasil_analisis' => '3',
        ]);

        // Data Pesan
        Pesan::create([
            'id' => '1',
            'id_staff' => '1',
            'id_konsultasi' => '1',
            'judul_pesan' => 'Rekomendasi Kendaraan Listrik untuk Fleet Management',
            'isi_pesan' => 'Berdasarkan analisis emisi, kami merekomendasikan transisi bertahap ke kendaraan listrik untuk mengurangi 60% emisi dari transportasi operasional.',
            'file_pdf' => 'rekomendasi_kendaraan_listrik.pdf',
        ]);

        Pesan::create([
            'id' => '2',
            'id_staff' => '2',
            'id_konsultasi' => '2',
            'judul_pesan' => 'Laporan Audit Energi Preliminary',
            'isi_pesan' => 'Hasil audit awal menunjukkan potensi penghematan energi hingga 30% melalui upgrade sistem HVAC dan implementasi smart lighting system.',
            'file_pdf' => 'audit_energi_preliminary.pdf',
        ]);

        Pesan::create([
            'id' => '3',
            'id_staff' => '1',
            'id_konsultasi' => '3',
            'judul_pesan' => 'Panduan Implementasi IoT Monitoring System',
            'isi_pesan' => 'Dokumentasi teknis untuk implementasi sistem monitoring berbasis IoT yang dapat mengintegrasikan data emisi dari berbagai sumber secara real-time.',
            'file_pdf' => 'panduan_iot_monitoring.pdf',
        ]);
    }
}
