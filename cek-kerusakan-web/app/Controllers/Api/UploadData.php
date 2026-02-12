<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class UploadData extends ResourceController
{
    public function upload()
    {
        // === Ambil file upload ===
        $file = $this->request->getFile('gambar');

        if (!$file || !$file->isValid()) {
            return $this->fail('File tidak valid atau tidak ditemukan.');
        }

        // Simpan file ke folder uploads
        $newName    = $file->getRandomName();
        $uploadPath = ROOTPATH . 'public/uploads/gambar/';
        $file->move($uploadPath, $newName);
        $fullImagePath = $uploadPath . $newName;


        // Ambil data lainnya dari POST
        $waktu_masuk     = $this->request->getPost('waktu_masuk') ?? date('Y-m-d H:i:s');
        $tempat          = $this->request->getPost('tempat') ?? 'UNKNOWN';
        $jenis_kerusakan = $this->request->getPost('jenis_kerusakan') ?? 'UNKNOWN';
        $nomor_kontainer = $this->request->getPost('nomor_kontainer') ?? 'UNKNOWN';

        // Simpan ke database
        $db = \Config\Database::connect();
        $db->table('kontainer')->insert([
            'nomor_kontainer' => $nomor_kontainer,
            'waktu_masuk'     => $waktu_masuk,
            'tempat'          => $tempat,
            'jenis_kerusakan' => $jenis_kerusakan,
            'gambar'          => "uploads/gambar/{$newName}",
        ]);

        // Kirim response
        return $this->respond([
            'message'          => 'Berhasil upload',
            'nomor_kontainer'  => $nomor_kontainer,
        ]);
    }
}
