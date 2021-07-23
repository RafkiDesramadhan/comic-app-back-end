<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    protected $komikModel;
    public function __construct()
    {
        $this->komikModel = new komikModel();
    }

    public function index()
    {
        // $komik = $this->komikModel->findAll();
        $data = [
            'title' => 'Daftar Komik | Web Programming Unpas',
            'komik' => $this->komikModel->getKomik()
        ];

        // $komikModel = new \App\Models\KomikModel(); //tanpa use
        // $komikModel = new KomikModel(); //menggunakan use diatas
        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/detail', $data);

        //jika komik tidak ada di tabel
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik' . $slug . 'Tidak Ditemukan');
        }
    }

    public function create()
    {
        //session(); udah di basecontroller //validation harus pake session
        $data = [
            'title' => 'Form Tambah Data Komik',
            'validation' => \Config\Services::validation()
        ];

        return view('komik/create', $data);
    }
    public function save()
    {
        // validasi input
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]', //[tabel.field/kolom]
                'errors' => [
                    'required' => '{field} harus diisi!',
                    'is_unique' => '{field} sudah ada!'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul, 1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            // mengambil pesan validasi
            // $validation = \Config\Services::validation(); jika menggunakan with input dimatikan
            return redirect()->to('/komik/create')->withInput(); //->with('validation', $validation);
        }

        $fileSampul = $this->request->getFile('sampul');
        //jika tidak ada gambar yang di upload
        // jika filesampul ada error, angka eror bisa bermacam2
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.jpg';
        } else {
            // membuat nama random
            $namaSampul = $fileSampul->getRandomName();
            //memindahkan file move() langsung ke folder public
            $fileSampul->move('img', $namaSampul);
            //membuat nama random
            //ambil nama file sampul
            //$namaSampul = $fileSampul->getName();
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        //mwmbuat flash data
        session()->setFlashdata('pesan', 'Data Berhasil Dimasukkan!');

        // setelah di save, mau dikembalikan ke halaman index
        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        //  cari data berdasarkann id
        $komik = $this->komikModel->find($id);
        //cek jika gambar default maka jangan dihapus
        if ($komik['sampul'] != 'default.jpg') {
            // hapus gambar
            unlink('img/' . $komik['sampul']);
        }

        //menghapus 
        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus!');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            'title' => "Form Edit Data Komik",
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/edit', $data);
    }

    // untuk proses edit
    public function update($id)
    {
        //cek judul lama
        $komikLama = $this->komikModel->getKomik($this->request->getVar('judul'));
        if ($komikLama == $this->request->getVar('slug')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} harus diisi!',
                    'is_unique' => '{field} sudah ada!'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul, 1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            // mengambil pesan validasi
            // $validation = \Config\Services::validation();
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput(); //->with('validation', $validation);
        }

        $fileSampul = $this->request->getFile('sampul');

        //cek gambar, apakah tetap gamba lama
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            // generate
            $namaSampul = $fileSampul->getRandomName();
            // pindahkan ke file img
            $fileSampul->move('img' . $namaSampul);
            // hapus file lama
            unlink('img/' . $this->request->getVar('sampulLama'));
        }

        $slug = url_title($this->request->getVar('judul'), '-', TRUE);
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);
        session()->setFlashdata('pesan', 'Data Berhasil Di Ubah!');
        return redirect()->to('/komik');
    }
}
