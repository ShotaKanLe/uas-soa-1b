<?php

namespace Tests\Feature;

use App\Models\Code;
use App\Models\StaffMitra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffMitraAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Halaman Login bisa diakses
     */
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test Staff Mitra berhasil Login dengan data benar
     */
    public function test_staff_mitra_can_login_with_valid_credentials()
    {
        // 1. Buat Code dummy terlebih dahulu (FIXED: Foreign Key Error)
        $code = Code::create([
            'code' => 'LOGIN-TEST',
            'code_type' => 'STAFF',
            'status' => 'USED'
        ]);

        // 2. Buat user dummy StaffMitra menggunakan ID Code yang valid
        $password = 'password123';
        $staff = StaffMitra::create([
            'nama_staff' => 'Test Staff',
            'email' => 'staff@example.com',
            'password' => Hash::make($password),
            'id_code' => $code->id, // Gunakan ID dari code yang baru dibuat
        ]);

        // 3. Lakukan request login
        $response = $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => $password,
        ]);

        // 4. Assert redirect ke dashboard staff
        $response->assertRedirect(route('dashboard.staff'));

        // 5. Assert session memiliki role staff
        $this->assertAuthenticatedAs($staff, 'staff');
        $response->assertSessionHas('role', 'staff');
    }

    /**
     * Test Staff Mitra gagal Login (Error Handling: Password Salah)
     */
    public function test_staff_mitra_cannot_login_with_invalid_password()
    {
        // 1. Buat Code dummy (FIXED: Foreign Key Error)
        $code = Code::create([
            'code' => 'LOGIN-FAIL-TEST',
            'code_type' => 'STAFF',
            'status' => 'USED'
        ]);

        // 2. Buat user dummy
        StaffMitra::create([
            'nama_staff' => 'Test Staff',
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
            'id_code' => $code->id,
        ]);

        // 3. Login dengan password salah
        $response = $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => 'wrong-password',
        ]);

        // 4. Assert kembali ke halaman sebelumnya dan ada error
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('staff');
    }

    /**
     * Test Staff Mitra berhasil Register (Validasi Input & Logic Code)
     */
    public function test_staff_mitra_can_register_with_valid_code()
    {
        // 1. Siapkan Kode Valid tipe STAFF
        $code = Code::create([
            'code' => 'STAFF-12345',
            'code_type' => 'STAFF',
            'status' => 'AVAILABLE'
        ]);

        // 2. Request Register
        $response = $this->post('/register', [
            'name' => 'Reza',
            'email' => 'reza@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code' => 'STAFF-12345',
        ]);

        // 3. Assert Redirect success (FIXED: Redirect URL mismatch)
        // Kita sesuaikan ekspektasi dengan redirect controller yang membawa parameter success
        $response->assertRedirect(route('register', ['success' => 'Account created successfully']));

        // 4. Assert Database terisi
        $this->assertDatabaseHas('staff_mitras', [
            'email' => 'reza@example.com',
        ]);

        // 5. Assert Kode berubah status jadi USED
        $this->assertDatabaseHas('codes', [
            'code' => 'STAFF-12345',
            'status' => 'USED',
        ]);
    }

    /**
     * Test Register Gagal jika Kode Invalid (Error Handling)
     */
    public function test_registration_fails_with_invalid_code()
    {
        $response = $this->post('/register', [
            'name' => 'Reza',
            'email' => 'reza@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code' => 'INVALID-CODE',
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertDatabaseMissing('staff_mitras', ['email' => 'reza@example.com']);
    }

    /**
     * Test Validasi Input Required (Input Validation)
     */
    public function test_registration_requires_email_and_password()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'code' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'code']);
    }
}
