<?php

namespace App\Http\Controllers;

use App\Models\News;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import untuk menangani error validasi secara spesifik
use Illuminate\Validation\ValidationException; // Import untuk menangani error umum

class NewsController extends Controller
{
    // 1. READ: Menampilkan daftar berita
    public function index()
    {
        $news = \App\Models\News::all();

        // View index Anda akan menampilkan pesan success/error dari session
        return view('dashboardStaff.layouts.news.index', compact('news'));
    }

    // 2. CREATE: Menampilkan form tambah berita
    public function add()
    {
        return view('dashboardStaff.layouts.news.create');
    }

    // 3. CREATE: Menyimpan data berita baru
    public function store(Request $request)
    {
        try {
            // Validasi Data (Laravel akan otomatis kembali ke form jika gagal, tidak perlu try-catch di sini)
            $validatedData = $request->validate([
                'title' => 'required|string|min:5|max:150|unique:news,title',
                'content' => 'required|string|min:20',
                'publisher' => 'required|string|max:100',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Logika Penyimpanan Gambar
            if ($request->hasFile('image')) {
                $imagePath              = $request->file('image')->store('news_images', 'public');
                $validatedData['image'] = $imagePath;
            }

            News::create($validatedData);

            // SUCCESS MESSAGE
            return redirect()->route('news.index')->with('success', 'News added successfully!');

        } catch (ValidationException $e) {
            // Error validasi ditangani otomatis oleh Laravel.
            throw $e;
        } catch (Exception $e) {
            // ERROR MESSAGE: Jika terjadi error server (mis. database down)
            return redirect()->route('news.index')->with('error', 'Failed to add news due to server error: '.$e->getMessage());
        }
    }

    // 4. UPDATE: Menampilkan form edit berita
    public function edit($id)
    {
        $news= News::findOrFail($id);

        return view('dashboardStaff.layouts.news.edit', compact('news'));
    }

    // 5. UPDATE: Menyimpan perubahan berita
    public function update(Request $request, $id)
    {
        try {
            $news = News::findOrFail($id);

            // Validasi Data
            $validatedData = $request->validate([
                'title' => 'required|string|min:5|max:150|unique:news,title,'.$news->id,
                'content' => 'required|string|min:20',
                'publisher' => 'required|string|max:100',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Logika Penyimpanan Gambar
            if ($request->hasFile('image')) {
                // Hapus gambar lama
                if ($news->image) {
                    Storage::disk('public')->delete($news->image);
                }
                $imagePath              = $request->file('image')->store('news_images', 'public');
                $validatedData['image'] = $imagePath;
            }

            $news->update($validatedData);

            // SUCCESS MESSAGE
            return redirect()->route('news.index')->with('success', 'Berita berhasil diperbarui.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            // ERROR MESSAGE
            return redirect()->route('news.index')->with('error', 'Failed to update news: '.$e->getMessage());
        }
    }

    // 6. DELETE: Menghapus data berita (hard delete)
    public function delete($id)
    {
        try {
            $news = News::findOrFail($id);

            // Hapus gambar dari storage
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $news->delete();

            // SUCCESS MESSAGE
            return redirect()->route('news.index')->with('success', 'Berita berhasil dihapus secara permanen.');

        } catch (Exception $e) {
            // ERROR MESSAGE
            return redirect()->route('news.index')->with('error', 'Gagal menghapus berita: '.$e->getMessage());
        }
    }

    // 7. RESTORE: Mengembalikan data berita (jika menggunakan soft delete)
    public function restore($id)
    {
        try {
            News::withTrashed()->findOrFail($id)->restore();

            return redirect()->route('news.index')->with('success', 'Berita berhasil dipulihkan.');
        } catch (Exception $e) {
            return redirect()->route('news.index')->with('error', 'Gagal memulihkan berita: '.$e->getMessage());
        }
    }

    // 8. Membuat show.bade.php
    public function show($id)
    {
        $news = News::findOrFail($id);

        return view('dashboardStaff.layouts.news.show', compact('news'));
    }
}
