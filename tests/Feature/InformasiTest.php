<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InformasiTest extends TestCase
{
    use RefreshDatabase;

    // UAS KELOMPOK 1B - TESTING INFORMASI
    // OLEH : FITRAH SEPTIANDWI SENSI - 2311082017

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_can_view_informasi_index_page()
    {

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

    public function test_can_view_add_informasi_page()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->get(route('informasi.add'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardStaff.layouts.informasi.add');
    }

    public function test_can_add_informasi()
    {
        Storage::fake('public');

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $file = UploadedFile::fake()->image('test_informasi.jpg');

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

    public function test_add_informasi_validation_fails()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->post(route('informasi.store'), [
            'information_name' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['information_name', 'content', 'gambar_informasi']);
    }

    public function test_can_view_edit_informasi_page()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->get(route('informasi.edit', 1));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardStaff.layouts.informasi.edit');
        $response->assertViewHas('oldData');
        $response->assertViewHas('id', 1);
    }

    public function test_can_update_informasi_without_image()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

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

    public function test_can_update_informasi_with_new_image()
    {
        Storage::fake('public');

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $file = UploadedFile::fake()->image('updated_informasi.jpg');

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

    public function test_update_informasi_validation_fails()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->put(route('informasi.update', 1), [
            'information_name' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['information_name', 'content']);
    }

    public function test_can_delete_informasi()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response = $this->delete(route('informasi.delete', 3));

        $response->assertRedirect('dashboard/staff/informasi');
        $response->assertSessionHas('success', 'Data Successfully Deleted');

        $this->assertDatabaseMissing('informasis', [
            'id' => 3,
            'deleted_at' => null,
        ]);
    }

    public function test_can_restore_informasi()
    {

        $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $this->delete(route('informasi.delete', 1));

        $response = $this->get(route('informasi.restore', 1));

        $response->assertRedirect('dashboard/perusahaan/informasi');
        $response->assertSessionHas('success', 'Data Successfully Restored');

        $this->assertDatabaseHas('informasis', [
            'id' => 1,
            'deleted_at' => null,
        ]);
    }

    public function test_cannot_access_informasi_without_login()
    {
        $response = $this->get(route('informasi.index'));

        $response->assertRedirect();
    }
}
