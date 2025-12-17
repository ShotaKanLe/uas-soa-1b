<?php

use App\Http\Controllers\AlamatRumahController;
use App\Http\Controllers\AnalisisEmisiKarbonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakarController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\KaryawanPerusahaanController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PerjalananKaryawanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\ServiceController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/register/service', [paymentController::class, 'index'])->name('register.service');
Route::post('/get-snap-token', [PaymentController::class, 'getSnapToken'])->name('getSnapToken');

Route::post('/set-payment-success', function () {
    session(['payment_success' => true]);

    return response()->json(['status' => 'ok']);
});

Route::get('/register/service/success', [PaymentController::class, 'paymentSuccess'])->name('register.success');
// Route::post('/midtrans/callback', [PaymentController::class, 'midtransCallback'])->name('midtrans.callback');
Route::post('/insertcompany', [PaymentController::class, 'insertFromFrontend']);
// routes/web.php (Laravel)
Route::get('/check-status/{order_id}', [PaymentController::class, 'checkStatus']);

Route::post('/store-location', [MapController::class, 'store'])->name('store.location');

Route::get('/login', [AuthController::class, 'viewLogin'])->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'viewRegister'])->name('register.view');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/portfolio', [HomeController::class, 'portfolio'])->name('portfolio');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::redirect('/dashboard/staff', '/dashboard/staff/perusahaan')->name('dashboard.staff');
Route::redirect('/dashboard/perusahaan', '/dashboard/perusahaan/karyawan')->name('dashboard.perusahaan');
Route::redirect('/dashboard/karyawan', '/dashboard/karyawan/perjalanan')->name('dashboard.karyawan');

Route::post('/generate-code', [CodeController::class, 'generateCode'])->name('code.generate');

// Perjalanan Karyawan Perusahaan
Route::get('dashboard/perusahaan/perjalanan', [PerjalananKaryawanController::class, 'index'])->name('perjalananKaryawanPerusahaan.index');
Route::get('dashboard/perusahaan/perjalanan/edit/{id}', [PerjalananKaryawanController::class, 'edit'])->name('perjalananKaryawanPerusahaan.edit');
Route::get('dashboard/perusahaan/perjalanan/add', [PerjalananKaryawanController::class, 'add'])->name('perjalananKaryawanPerusahaan.add');
Route::post('dashboard/perusahaan/perjalanan', [PerjalananKaryawanController::class, 'store'])->name('perjalananKaryawanPerusahaan.store');
Route::put('dashboard/perusahaan/perjalanan/edit/{id}', [PerjalananKaryawanController::class, 'update'])->name('perjalananKaryawanPerusahaan.update');
Route::delete('dashboard/perusahaan/perjalanan/{id}', [PerjalananKaryawanController::class, 'delete'])->name('perjalananKaryawanPerusahaan.delete');

// Alamat Rumah
Route::get('dashboard/perusahaan/alamat', [AlamatRumahController::class, 'index'])->name('alamatRumah.index');
Route::get('dashboard/perusahaan/alamat/edit/{id}', [AlamatRumahController::class, 'edit'])->name('alamatRumah.edit');
Route::get('dashboard/perusahaan/alamat/add', [AlamatRumahController::class, 'add'])->name('alamatRumah.add');
Route::post('dashboard/perusahaan/alamat', [AlamatRumahController::class, 'store'])->name('alamatRumah.store');
Route::put('dashboard/perusahaan/alamat/edit/{id}', [AlamatRumahController::class, 'update'])->name('alamatRumah.update');
Route::delete('dashboard/perusahaan/alamat/{id}', [AlamatRumahController::class, 'delete'])->name('alamatRumah.delete');
Route::get('dashboard/perusahaan/alamat/restore/{id}', [AlamatRumahController::class, 'restore'])->name('alamatRumah.restore');

// Perusahaan
Route::get('dashboard/staff/perusahaan', [PerusahaanController::class, 'index'])->name('perusahaan.index');
Route::get('dashboard/staff/perusahaan/edit/{id}', [PerusahaanController::class, 'edit'])->name('perusahaan.edit');
Route::put('dashboard/staff/perusahaan/edit/{id}', [PerusahaanController::class, 'update'])->name('perusahaan.update');
Route::delete('dashboard/staff/perusahaan/{id}', [PerusahaanController::class, 'delete'])->name('perusahaan.delete');
Route::get('dashboard/perusahaan/profile', [PerusahaanController::class, 'getCompanyProfile'])->name('perusahaan.profile');

// Service
Route::get('dashboard/staff/service', [ServiceController::class, 'index'])->name('service.index');
Route::get('dashboard/staff/service/edit/{id}', [ServiceController::class, 'edit'])->name('service.edit');
Route::get('dashboard/staff/service/add', [ServiceController::class, 'add'])->name('service.add');
Route::post('dashboard/staff/service', [ServiceController::class, 'store'])->name('service.store');
Route::put('dashboard/staff/service/edit/{id}', [ServiceController::class, 'update'])->name('service.update');
Route::delete('dashboard/staff/service/{id}', [ServiceController::class, 'delete'])->name('service.delete');
Route::get('dashboard/staff/service/restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');

// Bahan Bakar
Route::get('dashboard/staff/bahanBakar', [BahanBakarController::class, 'index'])->name('bahanBakar.index');
Route::get('dashboard/staff/bahanBakar/edit/{id}', [BahanBakarController::class, 'edit'])->name('bahanBakar.edit');
Route::get('dashboard/staff/bahanBakar/add', [BahanBakarController::class, 'add'])->name('bahanBakar.add');
Route::post('dashboard/staff/bahanBakar', [BahanBakarController::class, 'store'])->name('bahanBakar.store');
Route::put('dashboard/staff/bahanBakar/edit/{id}', [BahanBakarController::class, 'update'])->name('bahanBakar.update');
Route::delete('dashboard/staff/bahanBakar/{id}', [BahanBakarController::class, 'delete'])->name('bahanBakar.delete');
Route::get('dashboard/staff/bahanBakar/restore/{id}', [BahanBakarController::class, 'restore'])->name('bahanBakar.restore');

Route::get('dashboard/staff/informasi', [InformasiController::class, 'index'])->name('informasi.index');
Route::get('dashboard/staff/informasi/edit/{id}', [InformasiController::class, 'edit'])->name('informasi.edit');
Route::get('dashboard/staff/informasi/add', [InformasiController::class, 'add'])->name('informasi.add');
Route::post('dashboard/staff/informasi', [InformasiController::class, 'store'])->name('informasi.store');
Route::put('dashboard/staff/informasi/edit/{id}', [InformasiController::class, 'update'])->name('informasi.update');
Route::delete('dashboard/staff/informasi/{id}', [InformasiController::class, 'delete'])->name('informasi.delete');
Route::get('dashboard/staff/informasi/restore/{id}', [InformasiController::class, 'restore'])->name('informasi.restore');

// News
Route::get('dashboard/staff/news', [NewsController::class, 'index'])->name('news.index');
Route::get('dashboard/staff/news/edit/{id}', [NewsController::class, 'edit'])->name('news.edit');
Route::get('dashboard/staff/news/add', [NewsController::class, 'add'])->name('news.create');
Route::post('dashboard/staff/news', [NewsController::class, 'store'])->name('news.store');
Route::put('dashboard/staff/news/edit/{id}', [NewsController::class, 'update'])->name('news.update');
Route::delete('dashboard/staff/news/{id}', [NewsController::class, 'delete'])->name('news.delete');
Route::get('dashboard/staff/news/restore/{id}', [NewsController::class, 'restore'])->name('news.restore');
Route::get('/staff/news/{id}', [NewsController::class, 'show'])->name('news.show');

Route::get('dashboard/staff/konsultasi', [KonsultasiController::class, 'indexStaff'])->name('staff.konsultasi.index');
Route::get('dashboard/staff/konsultasi/edit/{id}', [KonsultasiController::class, 'editStaff'])->name('staff.konsultasi.edit');
Route::get('dashboard/staff/konsultasi/add', [KonsultasiController::class, 'addStaff'])->name('staff.konsultasi.add');
Route::post('dashboard/staff/konsultasi/add', [KonsultasiController::class, 'replyStaff'])->name('staff.konsultasi.reply');
Route::post('dashboard/staff/konsultasi', [KonsultasiController::class, 'storeStaff'])->name('staff.konsultasi.store');
Route::put('dashboard/staff/konsultasi/edit/{id}', [KonsultasiController::class, 'updateStaff'])->name('staff.konsultasi.update');
Route::delete('dashboard/staff/konsultasi/{id}', [KonsultasiController::class, 'deleteStaff'])->name('staff.konsultasi.delete');
Route::get('dashboard/staff/konsultasi/restore/{id}', [KonsultasiController::class, 'restoreStaff'])->name('staff.konsultasi.restore');
Route::post('dashboard/staff/konsultasi/comments', [KonsultasiController::class, 'comments'])->name('staff.konsultasi.comments');

Route::get('dashboard/perusahaan/karyawan', [KaryawanPerusahaanController::class, 'index'])->name('karyawan.index');
Route::get('dashboard/perusahaan/karyawan/edit/{id}', [KaryawanPerusahaanController::class, 'edit'])->name('karyawan.edit');
Route::get('dashboard/perusahaan/karyawan/add', [KaryawanPerusahaanController::class, 'add'])->name('karyawan.add');
Route::post('dashboard/perusahaan/karyawan', [KaryawanPerusahaanController::class, 'store'])->name('karyawan.store');
Route::put('dashboard/perusahaan/karyawan/edit/{id}', [KaryawanPerusahaanController::class, 'update'])->name('karyawan.update');
Route::delete('dashboard/perusahaan/karyawan/{id}', [KaryawanPerusahaanController::class, 'delete'])->name('karyawan.delete');
Route::get('dashboard/perusahaan/karyawan/restore/{id}', [KaryawanPerusahaanController::class, 'restore'])->name('karyawan.restore');

Route::get('dashboard/perusahaan/analisis', [AnalisisEmisiKarbonController::class, 'index'])->name('analisis.index');
Route::get('dashboard/perusahaan/analisis/edit/{id}', [AnalisisEmisiKarbonController::class, 'edit'])->name('analisis.edit');
Route::put('dashboard/perusahaan/analisis/edit/{id}', [AnalisisEmisiKarbonController::class, 'update'])->name('analisis.update');
Route::delete('dashboard/perusahaan/analisis/{id}', [AnalisisEmisiKarbonController::class, 'delete'])->name('analisis.delete');
Route::get('dashboard/perusahaan/analisis/restore/{id}', [AnalisisEmisiKarbonController::class, 'restore'])->name('analisis.restore');
Route::get('dashboard/perusahaan/analisis/tabelanalisis', [AnalisisEmisiKarbonController::class, 'viewAnalisis'])->name('analisis.viewAnalisis');
Route::get('dashboard/perusahaan/analisis-pdf', [AnalisisEmisiKarbonController::class, 'prosesAnalisis'])->name('analisis.analisis');
// Route::post('dashboard/perusahaan/analisis/pdf', [AnalisisEmisiKarbonController::class, 'exportPdf'])->name('analisis.pdf');

Route::get('dashboard/perusahaan/konsultasi', [KonsultasiController::class, 'index'])->name('konsultasi.index');
Route::get('dashboard/perusahaan/konsultasi/edit/{id}', [KonsultasiController::class, 'edit'])->name('konsultasi.edit');
Route::get('dashboard/perusahaan/konsultasi/add', [KonsultasiController::class, 'add'])->name('konsultasi.add');
Route::post('dashboard/perusahaan/konsultasi/add', [KonsultasiController::class, 'upload'])->name('konsultasi.upload');
Route::post('dashboard/perusahaan/konsultasi', [KonsultasiController::class, 'store'])->name('konsultasi.store');
Route::put('dashboard/perusahaan/konsultasi/edit/{id}', [KonsultasiController::class, 'update'])->name('konsultasi.update');
Route::delete('dashboard/perusahaan/konsultasi/{id}', [KonsultasiController::class, 'delete'])->name('konsultasi.delete');
Route::get('dashboard/perusahaan/konsultasi/restore/{id}', [KonsultasiController::class, 'restore'])->name('konsultasi.restore');

Route::get('dashboard/karyawan/', [KaryawanPerusahaanController::class, 'homeKaryawan'])->name('karyawan.home');
Route::post('dashboard/absen/', [PerjalananKaryawanController::class, 'absen'])->name('absen');
Route::get('dashboard/karyawan/perjalanan', [PerjalananKaryawanController::class, 'indexKaryawan'])->name('karyawan.perjalanan.index');
Route::get('dashboard/karyawan/perjalanan/edit/{id}', [PerjalananKaryawanController::class, 'editKaryawan'])->name('karyawan.perjalanan.edit');
Route::get('dashboard/karyawan/perjalanan/add', [PerjalananKaryawanController::class, 'addKaryawan'])->name('karyawan.perjalanan.add');
Route::post('dashboard/karyawan/perjalanan/add', [PerjalananKaryawanController::class, 'uploadKaryawan'])->name('karyawan.perjalanan.upload');
Route::post('dashboard/karyawan/perjalanan', [PerjalananKaryawanController::class, 'storeKaryawan'])->name('karyawan.perjalanan.store');
Route::put('dashboard/karyawan/perjalanan/edit/{id}', [PerjalananKaryawanController::class, 'updateKaryawan'])->name('karyawan.perjalanan.update');
Route::delete('dashboard/karyawan/perjalanan/{id}', [PerjalananKaryawanController::class, 'deleteKaryawan'])->name('karyawan.perjalanan.delete');
Route::get('dashboard/karyawan/perjalanan/restore/{id}', [PerjalananKaryawanController::class, 'restoreKaryawan'])->name('karyawan.perjalanan.restore');

Route::get('dashboard/karyawan/alamat', [AlamatRumahController::class, 'indexAlamatKaryawan'])->name('karyawan.alamat.index');
Route::get('dashboard/karyawan/alamat/edit/{id}', [AlamatRumahController::class, 'editAlamatKaryawan'])->name('karyawan.alamat.edit');
Route::get('dashboard/karyawan/alamat/add', [AlamatRumahController::class, 'addAlamatKaryawan'])->name('karyawan.alamat.add');
Route::post('dashboard/karyawan/alamat/add', [AlamatRumahController::class, 'storeAlamatKaryawan'])->name('karyawan.alamat.store');
Route::post('dashboard/karyawan/alamat', [AlamatRumahController::class, 'storeAlamatKaryawan'])->name('karyawan.alamat.store');
Route::put('dashboard/karyawan/alamat/edit/{id}', [AlamatRumahController::class, 'updateAlamatKaryawan'])->name('karyawan.alamat.update');
Route::delete('dashboard/karyawan/alamat/{id}', [AlamatRumahController::class, 'deleteAlamatKaryawan'])->name('karyawan.alamat.delete');
Route::get('dashboard/karyawan/alamat/restore/{id}', [AlamatRumahController::class, 'restoreAlamatKaryawan'])->name('karyawan.alamat.restore');

Route::get('/analysis/download/{filename}', [AnalisisEmisiKarbonController::class, 'downloadAnalysisPdf'])->name('analysis.pdf.download');
Route::get('/reply/download/{filename}', [AnalisisEmisiKarbonController::class, 'downloadReplyPdf'])->name('reply.pdf.download');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/session', function () {
    return session()->all();
});
