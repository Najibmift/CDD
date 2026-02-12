<?php

namespace App\Models;

use CodeIgniter\Model;

class KontainerModel extends Model
{
    protected $table      = 'kontainer';
    protected $primaryKey = 'id';

    protected $allowedFields = ['nomor_kontainer', 'waktu_masuk', 'tempat', 'jenis_kerusakan', 'gambar'];
}
