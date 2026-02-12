<?php

namespace App\Controllers;

use App\Models\KontainerModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $model = new KontainerModel();

        $now = date('Y-m-d');

        // Harian & Kemarin
        $hariIni = $model->where('DATE(waktu_masuk)', $now)->countAllResults();

        $kemarin = $model
            ->where('DATE(waktu_masuk)', date('Y-m-d', strtotime('-1 day')))
            ->countAllResults();

        // Mingguan & Minggu Lalu
        $mingguIni = $model
            ->where("YEARWEEK(waktu_masuk, 1) = YEARWEEK(CURDATE(), 1)", null, false)
            ->countAllResults();

        $mingguLalu = $model
            ->where("YEARWEEK(waktu_masuk, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)", null, false)
            ->countAllResults();

        // Bulanan & Bulan Lalu
        $bulanIni = $model
            ->where('MONTH(waktu_masuk)', date('m'))
            ->where('YEAR(waktu_masuk)', date('Y'))
            ->countAllResults();

        $bulanLalu = $model
            ->where('MONTH(waktu_masuk)', date('m', strtotime('-1 month')))
            ->where('YEAR(waktu_masuk)', date('Y', strtotime('-1 month')))
            ->countAllResults();

        // Tahunan & Tahun Lalu
        $tahunIni = $model
            ->where('YEAR(waktu_masuk)', date('Y'))
            ->countAllResults();

        $tahunLalu = $model
            ->where('YEAR(waktu_masuk)', date('Y', strtotime('-1 year')))
            ->countAllResults();

        // Data grafik: kerusakan bulanan per jenis
        // $grafikData = [
        //     // 'Lubang' => [],
        //     // 'Penyok' => [],
        //     // 'Sobek' => [],
        //     'Damage' => []
        // ];

        // for ($i = 1; $i <= 12; $i++) {
        //     foreach (['Lubang', 'Penyok', 'Sobek'] as $jenis) {
        //         $count = (new KontainerModel())
        //             ->where('MONTH(waktu_masuk)', $i)
        //             ->where('YEAR(waktu_masuk)', date('Y'))
        //             ->where('jenis_kerusakan', $jenis)
        //             ->countAllResults();
        //         $grafikData[$jenis][] = $count;
        //     }
        // }

        // // Cari nilai maksimum dari semua data grafik
        // // Cari nilai maksimum dari satu jenis kerusakan dalam satu bulan (selama 12 bulan)
        // $maxValue = 0;
        // foreach ($grafikData as $jenis => $data) {
        //     foreach ($data as $bulan => $jumlah) {
        //         if ($jumlah > $maxValue) {
        //             $maxValue = $jumlah;
        //         }
        //     }
        // }

        // // Tambahkan margin (opsional)
        // $maxValue += 2;
        // Grafik bulanan hanya untuk 'Damage'
        $grafikData = [
            'Damage' => [],
        ];

        for ($i = 1; $i <= 12; $i++) {
            $count = $model
                ->where('MONTH(waktu_masuk)', $i)
                ->where('YEAR(waktu_masuk)', date('Y'))
                ->where('jenis_kerusakan', 'Damage')
                ->countAllResults();
            $grafikData['Damage'][] = $count;
        }

        // Cari nilai maksimum grafik
        $maxValue = max($grafikData['Damage']);
        $maxValue += 2;


        $data = [
            'maxValue' => $maxValue,
            'hari' => $hariIni,
            'selisihHari' => $hariIni - $kemarin,

            'minggu' => $mingguIni,
            'selisihMinggu' => $mingguIni - $mingguLalu,

            'bulan' => $bulanIni,
            'selisihBulan' => $bulanIni - $bulanLalu,

            'tahun' => $tahunIni,
            'selisihTahun' => $tahunIni - $tahunLalu,

            'grafikData' => $grafikData,

            'nama_user' => session()->get('nama')
        ];

        return view('dashboard', $data);
    }
}
