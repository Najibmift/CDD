<?php

namespace App\Controllers;

use App\Models\KontainerModel;
use CodeIgniter\Controller;

class CariKontainer extends BaseController
{
    public function index()
    {
        $model = new KontainerModel();
        $data['kontainer'] = $model->orderBy('waktu_masuk', 'DESC')->limit(5)->find(); // default 5 terakhir
        return view('cari_kontainer', $data);
    }

    public function search()
    {
        $model = new KontainerModel();
        $keyword = $this->request->getVar('keyword');
        $page = (int) ($this->request->getVar('page') ?? 1);
        $perPage = 5;

        $builder = $model->like('nomor_kontainer', $keyword);
        $total = $builder->countAllResults(false); // jangan reset query
        $kontainer = $builder->orderBy('waktu_masuk', 'DESC')
            ->paginate($perPage, 'default', $page);

        // Data format
        $formatted = array_map(function ($item) {
            return [
                'nomor' => $item['nomor_kontainer'],
                'waktu' => $item['waktu_masuk'],
                'tempat' => $item['tempat'],
                'jenis_kerusakan' => $item['jenis_kerusakan'],
                'gambar' => base_url($item['gambar']),
            ];
        }, $kontainer);

        return $this->response->setJSON([
            'data' => $formatted,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage)
        ]);
    }
}
