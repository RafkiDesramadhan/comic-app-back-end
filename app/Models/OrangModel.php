<?php
// cara membuat model database
namespace App\Models;

use CodeIgniter\Model;

class OrangModel extends Model
{
    // config database jangan lupa cek defaultnya di parent Model
    protected $table = 'orang';
    protected $useTimestamps = true;
    // kita harus memberitahu apa yang akan kita isi di database untuk keamanan
    protected $allowedFields = ['nama', 'alamat'];

    public function search($keyword)
    {
        // memakai builder cari aje di documentasi
        // $builder = $this->table('orang');
        // $builder->like('nama', $keyword);
        // return $builder;

        return $this->table('orang')->like('nama', $keyword)->orLike('alamat', $keyword);
    }
}
