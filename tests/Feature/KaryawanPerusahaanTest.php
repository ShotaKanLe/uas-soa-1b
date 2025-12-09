<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class KaryawanPerusahaanTest extends TestCase
{
    use RefreshDatabase;

    // UAS KELOMPOK 1B - TESTING KARYAWAN PERUSAHAAN
    // OLEH : [NAMA] - [NIM]

    protected function setUp(): void
    {
        parent::setUp();

        // Jalankan seeder untuk populate data
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test halaman login dapat diakses
     */
    public function test_can_view_login_page()
    {
        $response = $this->get(route('login.view'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test login karyawan berhasil dengan data seeder
     */
    public function test_karyawan_can_login()
    {
        // Gunakan data karyawan dari seeder
        // Email: karyawan@example.com, Password: karyawan
        $response = $this->post(route('login'), [
            'email' => 'karyawan@example.com',
            'password' => 'karyawan',
        ]);

        $response->assertRedirect(route('dashboard.karyawan'));
        $this->assertEquals('karyawan', session('role'));
        $this->assertEquals('Andi Pratama', session('name'));
    }

    /**
     * Test login staff perusahaan berhasil dengan data seeder
     */
    public function test_staff_perusahaan_can_login()
    {
        // Gunakan data staff perusahaan dari seeder
        // Email: staff@example.com, Password: staff
        $response = $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        $response->assertRedirect(route('dashboard.perusahaan'));
        $this->assertEquals('perusahaan', session('role'));
        $this->assertEquals('Budi Santoso', session('name'));
    }

    /**
     * Test login staff mitra berhasil dengan data seeder
     */
    public function test_staff_mitra_can_login()
    {
        // Gunakan data staff mitra dari seeder
        // Email: admin@example.com, Password: admin
        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect(route('dashboard.staff'));
        $this->assertEquals('staff', session('role'));
        $this->assertEquals('Dr. Ahmad Rizki', session('name'));
    }

    /**
     * Test login dengan kredensial salah
     */
    public function test_login_with_invalid_credentials()
    {
        $response = $this->post(route('login'), [
            'email' => 'invalid@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test halaman register dapat diakses
     */
    public function test_can_view_register_page()
    {
        $response = $this->get(route('register.view'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /**
     * Test register dengan email yang sudah terdaftar
     */
    public function test_register()
    {
        $response = $this->post(route('register'), [
            'name' => 'Duplicate User',
            'email' => 'karyawan@example.com', // Email yang sudah ada di seeder
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code' => 'SM2024002',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test edit karyawan berhasil
     */
    public function test_can_update_karyawan()
    {
        // Login sebagai staff perusahaan dari seeder
        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        // Update data karyawan dengan id 2 (Dewi Lestari)
        $response = $this->put(route('karyawan.update', 2), [
            'employee_name' => 'Dewi Lestari Updated',
            'position' => 'Senior Sustainability Manager',
            'email' => 'dewi.updated@teknologihijau.co.id',
            'gender' => 'P',
            'birth_date' => '1988-12-22',
        ]);

        $response->assertRedirect(route('karyawan.edit', 2));
        $response->assertSessionHas('success', 'Data Successfully Updated');

        $this->assertDatabaseHas('karyawan_perusahaans', [
            'id' => 2,
            'nama_karyawan' => 'Dewi Lestari Updated',
            'jabatan' => 'Senior Sustainability Manager',
            'email' => 'dewi.updated@teknologihijau.co.id',
        ]);
    }

    /**
     * Test delete karyawan berhasil (soft delete)
     */
    public function test_can_delete_karyawan()
    {
        // Login sebagai staff perusahaan dari seeder
        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        // Delete karyawan dengan id 2 (Dewi Lestari)
        $response = $this->delete(route('karyawan.delete', 2));

        $response->assertRedirect('dashboard/perusahaan/karyawan');
        $response->assertSessionHas('success', 'Data Successfully Deleted');

        // Cek apakah data ter-soft delete
        $this->assertSoftDeleted('karyawan_perusahaans', [
            'id' => 2,
        ]);
    }

    /**
     * Test logout karyawan berhasil
     */
    public function test_karyawan_can_logout()
    {
        // Login sebagai karyawan
        $this->post(route('login'), [
            'email' => 'karyawan@example.com',
            'password' => 'karyawan',
        ]);

        // Pastikan sudah login
        $this->assertEquals('karyawan', session('role'));

        // Logout
        $response = $this->get(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertNull(session('role'));
        $this->assertNull(session('id'));
    }

    /**
     * Test logout staff perusahaan berhasil
     */
    public function test_staff_perusahaan_can_logout()
    {
        // Login sebagai staff perusahaan
        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        // Pastikan sudah login
        $this->assertEquals('perusahaan', session('role'));

        // Logout
        $response = $this->get(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertNull(session('role'));
        $this->assertNull(session('id'));
    }

    /**
     * Test akses halaman edit karyawan
     */
    public function test_can_view_edit_karyawan_page()
    {
        // Login sebagai staff perusahaan
        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        // Akses halaman edit karyawan id 1
        $response = $this->get(route('karyawan.edit', 1));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardPerusahaan.layouts.karyawan.edit');
        $response->assertViewHas('oldData');
    }
}
