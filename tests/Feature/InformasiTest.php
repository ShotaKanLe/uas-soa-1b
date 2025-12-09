<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class InformasiTest extends TestCase
{
    use RefreshDatabase;

    // UAS KELOMPOK 1B - TESTING INFORMASI
    // OLEH : FITRAH SEPTIANDWI SENSI - 2311082017

    protected function setUp(): void
    {
        parent::setUp();

        // Jalankan seeder untuk populate data
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test halaman index informasi dapat diakses
     */
    public function test_can_view_informasi_index_page()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->get(route('informasi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardStaff.layouts.informasi.view');
        $response->assertViewHas('data');
        $response->assertViewHas('dataType', 'informasi');
    }

    /**
     * Test halaman add informasi dapat diakses
     */
    public function test_can_view_add_informasi_page()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->get(route('informasi.add'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardStaff.layouts.informasi.add');
    }

    /**
     * Test tambah informasi berhasil
     */
    public function test_can_add_informasi()
    {
        Storage::fake('public');

        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Create fake image
        $file = UploadedFile::fake()->image('test_informasi.jpg');

        // Tambah data informasi baru
        $response = $this->post(route('informasi.store'), [
            'information_name' => 'Informasi Baru Test',
            'content' => 'Ini adalah konten informasi test yang baru ditambahkan untuk keperluan unit testing.',
            'gambar_informasi' => $file,
        ]);

        $response->assertRedirect('dashboard/staff/informasi/add');
        $response->assertSessionHas('success', 'Data Successfully Added');

        $this->assertDatabaseHas('informasis', [
            'judul_informasi' => 'Informasi Baru Test',
            'isi_informasi' => 'Ini adalah konten informasi test yang baru ditambahkan untuk keperluan unit testing.',
            'id_staff_mitra' => 1,
        ]);
    }

    /**
     * Test tambah informasi gagal karena validasi
     */
    public function test_add_informasi_validation_fails()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Tambah data informasi tanpa field required
        $response = $this->post(route('informasi.store'), [
            'information_name' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['information_name', 'content', 'gambar_informasi']);
    }

    /**
     * Test halaman edit informasi dapat diakses
     */
    public function test_can_view_edit_informasi_page()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Akses halaman edit informasi id 1
        $response = $this->get(route('informasi.edit', 1));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardStaff.layouts.informasi.edit');
        $response->assertViewHas('oldData');
        $response->assertViewHas('id', 1);
    }

    /**
     * Test edit informasi berhasil tanpa mengganti gambar
     */
    public function test_can_update_informasi_without_image()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Update data informasi dengan id 1 (tanpa mengganti gambar)
        $response = $this->put(route('informasi.update', 1), [
            'information_name' => 'Tips Mengurangi Jejak Karbon di Tempat Kerja Updated',
            'content' => 'Konten artikel yang telah diperbarui dengan informasi terbaru mengenai strategi pengurangan emisi karbon.',
        ]);

        $response->assertRedirect('dashboard/staff/informasi/edit/1');
        $response->assertSessionHas('success', 'Data Successfully Updated');

        $this->assertDatabaseHas('informasis', [
            'id' => 1,
            'judul_informasi' => 'Tips Mengurangi Jejak Karbon di Tempat Kerja Updated',
            'isi_informasi' => 'Konten artikel yang telah diperbarui dengan informasi terbaru mengenai strategi pengurangan emisi karbon.',
        ]);
    }

    /**
     * Test edit informasi berhasil dengan mengganti gambar
     */
    public function test_can_update_informasi_with_new_image()
    {
        Storage::fake('public');

        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Create fake image
        $file = UploadedFile::fake()->image('updated_informasi.jpg');

        // Update data informasi dengan id 2 (dengan mengganti gambar)
        $response = $this->put(route('informasi.update', 2), [
            'information_name' => 'Pentingnya Monitoring Emisi Karbon Updated',
            'content' => 'Artikel yang diperbarui tentang monitoring emisi karbon dengan metodologi terbaru.',
            'gambar_informasi' => $file,
        ]);

        $response->assertRedirect('dashboard/staff/informasi/edit/2');
        $response->assertSessionHas('success', 'Data Successfully Updated');

        $this->assertDatabaseHas('informasis', [
            'id' => 2,
            'judul_informasi' => 'Pentingnya Monitoring Emisi Karbon Updated',
            'isi_informasi' => 'Artikel yang diperbarui tentang monitoring emisi karbon dengan metodologi terbaru.',
        ]);
    }

    /**
     * Test edit informasi gagal karena validasi
     */
    public function test_update_informasi_validation_fails()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Update informasi tanpa field required
        $response = $this->put(route('informasi.update', 1), [
            'information_name' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['information_name', 'content']);
    }

    /**
     * Test delete informasi berhasil
     */
    public function test_can_delete_informasi()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Delete informasi dengan id 3
        $response = $this->delete(route('informasi.delete', 3));

        $response->assertRedirect('dashboard/staff/informasi');
        $response->assertSessionHas('success', 'Data Successfully Deleted');

        // Cek apakah data terhapus dari database
        $this->assertDatabaseMissing('informasis', [
            'id' => 3,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test restore informasi berhasil
     */
    public function test_can_restore_informasi()
    {
        // Login sebagai staff mitra dari seeder
        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        // Hapus informasi terlebih dahulu
        $this->delete(route('informasi.delete', 1));

        // Restore informasi
        $response = $this->get(route('informasi.restore', 1));

        $response->assertRedirect('dashboard/perusahaan/informasi');
        $response->assertSessionHas('success', 'Data Successfully Restored');

        // Cek apakah data sudah di-restore
        $this->assertDatabaseHas('informasis', [
            'id' => 1,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test akses halaman informasi tanpa login
     */
    public function test_cannot_access_informasi_without_login()
    {
        $response = $this->get(route('informasi.index'));

        // Pastikan redirect ke login atau halaman lain
        $response->assertRedirect();
    }
}
