<?php

/**
 * Pesan validasi Bahasa Indonesia. Rule yang belum diterjemahkan memakai versi Inggris.
 */
return array_replace_recursive(require __DIR__.'/../en/validation.php', [
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password saat ini salah.',
    'different' => ':attribute harus berbeda dengan :other.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'max' => [
        'file' => ':attribute maksimal :max kilobyte.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
    ],
    'password' => [
        'letters' => ':attribute harus mengandung minimal satu huruf.',
        'numbers' => ':attribute harus mengandung minimal satu angka.',
    ],
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',

    'attributes' => [
        'area_id' => 'area',
        'category_id' => 'kategori',
        'location_detail' => 'lokasi detail',
        'description' => 'deskripsi masalah',
        'urgency' => 'tingkat urgensi',
        'photo' => 'foto kerusakan',
        'completion_photo' => 'foto bukti perbaikan',
        'assigned_to' => 'teknisi',
        'note' => 'catatan',
        'name' => 'nama',
        'code' => 'kode',
        'icon' => 'icon',
        'role' => 'role',
        'current_password' => 'password saat ini',
        'password' => 'password',
    ],
]);
