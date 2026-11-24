<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'mahasiswa2@celoe.test'],
            [
                'name' => 'Dyandra Putra',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '102042500152',
            ]
        );

        $basisData = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF302'],
            [
                'nama_mk' => 'Basis Data',
                'deskripsi' => 'Perancangan basis data relasional, normalisasi tabel, dan kueri SQL.',
                'sks' => 3,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $algoritma = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF303'],
            [
                'nama_mk' => 'Algoritma dan Pemrograman',
                'deskripsi' => 'Belajar C# bareng abang asprak.',
                'sks' => 4,
                'semester' => 'Ganjil 2027/2028',
                'dosen_id' => $dosen->id,
            ]
        );

        foreach ([$basisData, $algoritma] as $course) {
            $course->students()->syncWithoutDetaching([
                $mahasiswa1->id => ['status' => 'approved'],
                $mahasiswa2->id => ['status' => 'approved'],
            ]);
        }

        $tugasBasisData = Assignment::firstOrCreate(
            ['course_id' => $basisData->id, 'judul' => 'Normalisasi Basis Data Perpustakaan'],
            [
                'deskripsi' => 'Jelaskan cara memisahkan data buku, anggota, dan peminjaman hingga bentuk normal ketiga.',
                'deadline' => now()->addWeek(),
            ]
        );

        $tugasAlgoritma = Assignment::firstOrCreate(
            ['course_id' => $algoritma->id, 'judul' => 'Evaluasi Kegunaan Formulir Pendaftaran'],
            [
                'deskripsi' => 'Identifikasi dua masalah kegunaan pada formulir pendaftaran dan usulkan perbaikannya.',
                'deadline' => now()->addWeeks(2),
            ]
        );

        Submission::firstOrCreate(
            ['assignment_id' => $tugasBasisData->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Pisahkan buku, anggota, dan peminjaman ke tabel tersendiri. Tabel peminjaman menyimpan ID buku dan ID anggota sebagai kunci asing.',
                'submitted_at' => now(),
                'nilai' => 85,
                'catatan_dosen' => 'Relasi sudah tepat. Jelaskan juga alasan pemisahan tabel untuk menghindari ketergantungan transitif.',
            ]
        );

        Submission::firstOrCreate(
            ['assignment_id' => $tugasBasisData->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Simpan setiap judul buku satu kali di tabel buku. Riwayat peminjaman merujuk ID buku dan ID anggota sehingga data tidak berulang.',
                'submitted_at' => now(),
            ]
        );

        Submission::firstOrCreate(
            ['assignment_id' => $tugasAlgoritma->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Formulir perlu memperbesar ukuran teks agar lebih mudah dibaca.',
                'submitted_at' => now(),
                'nilai' => 60,
                'catatan_dosen' => 'Tambahkan satu masalah kegunaan lagi dan jelaskan solusi untuk masing-masing masalah.',
            ]
        );
    }
}