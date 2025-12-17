<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryawanPerusahaanTest extends TestCase
{
    use RefreshDatabase;

    // UAS KELOMPOK 1B - TESTING KARYAWAN PERUSAHAAN
    // OLEH : Fakhreza Aldino - 2311083003

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_can_view_login_page()
    {
        $response = $this->get(route('login.view'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_karyawan_can_login()
    {

        $response = $this->post(route('login'), [
            'email' => 'karyawan@example.com',
            'password' => 'karyawan',
        ]);

        $response->assertRedirect(route('dashboard.karyawan'));
        $this->assertEquals('karyawan', session('role'));
        $this->assertEquals('Andi Pratama', session('name'));
    }

    public function test_staff_perusahaan_can_login()
    {

        $response = $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        $response->assertRedirect(route('dashboard.perusahaan'));
        $this->assertEquals('perusahaan', session('role'));
        $this->assertEquals('Budi Santoso', session('name'));
    }

    public function test_staff_mitra_can_login()
    {

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ]);

        $response->assertRedirect(route('dashboard.staff'));
        $this->assertEquals('staff', session('role'));
        $this->assertEquals('Dr. Ahmad Rizki', session('name'));
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->post(route('login'), [
            'email' => 'invalid@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_can_view_register_page()
    {
        $response = $this->get(route('register.view'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_register()
    {
        $response = $this->post(route('register'), [
            'name' => 'Duplicate User',
            'email' => 'karyawan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code' => 'SM2024002',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    public function test_can_update_karyawan()
    {

        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

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

    public function test_can_delete_karyawan()
    {

        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        $response = $this->delete(route('karyawan.delete', 2));

        $response->assertRedirect('dashboard/perusahaan/karyawan');
        $response->assertSessionHas('success', 'Data Successfully Deleted');

        $this->assertSoftDeleted('karyawan_perusahaans', [
            'id' => 2,
        ]);
    }

    public function test_karyawan_can_logout()
    {

        $this->post(route('login'), [
            'email' => 'karyawan@example.com',
            'password' => 'karyawan',
        ]);

        $this->assertEquals('karyawan', session('role'));

        $response = $this->get(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertNull(session('role'));
        $this->assertNull(session('id'));
    }

    public function test_staff_perusahaan_can_logout()
    {

        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        $this->assertEquals('perusahaan', session('role'));

        $response = $this->get(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertNull(session('role'));
        $this->assertNull(session('id'));
    }

    public function test_can_view_edit_karyawan_page()
    {

        $this->post(route('login'), [
            'email' => 'staff@example.com',
            'password' => 'staff',
        ]);

        $response = $this->get(route('karyawan.edit', 1));

        $response->assertStatus(200);
        $response->assertViewIs('dashboardPerusahaan.layouts.karyawan.edit');
        $response->assertViewHas('oldData');
    }
}
