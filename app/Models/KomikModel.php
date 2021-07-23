<?php
// cara membuat model database
namespace App\Models;

use CodeIgniter\Model;

class KomikModel extends Model
{
    // config database jangan lupa cek defaultnya di parent Model
    protected $table = 'komik';
    protected $useTimestamps = true;
    // kita harus memberitahu apa yang akan kita isi di database untuk keamanan
    protected $allowedFields = ['judul', 'slug', 'penulis', 'penerbit', 'sampul'];

    public function getKomik($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }
        // jika ada slug
        return $this->where(['slug' => $slug])->first();
    }
}
