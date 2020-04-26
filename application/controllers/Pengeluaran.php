<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class pengeluaran extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    //Menampilkan data kontak
    function index_get() {
        $id = $this->get('tanggal');
        if ($id == '') {
            $kontak = $this->db->query('SELECT *, SUM(jumlah) AS jumlah_total FROM tb_pengeluaran GROUP BY tanggal')->result();
        } else {
            $this->db->where('tanggal', $id);
            $kontak = $this->db->get('tb_pemasukan')->result();
        }
        $this->response($kontak, 200);
    }

    //Masukan function selanjutnya disini
}
?>