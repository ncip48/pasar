<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class pemasukan extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

    //Menampilkan data kontak
    function index_get() {
        $id = $this->get('tanggal');
        if ($id == '') {
            $kontak = $this->db->query("SELECT *, DATE_FORMAT(tanggal, '%d/%m') AS tanggal, SUM(jumlah) AS jumlah_total FROM tb_pemasukan GROUP BY tanggal")->result();
        } else {
            $this->db->where('tanggal', $id);
            $kontak = $this->db->get('tb_pemasukan')->result();
        }
        $this->response($kontak, 200);
    }

    function laba_get(){
        $datenow = date("Y-m-d");
        $dateyes = date("Y-m-d", strtotime('yesterday'));
        $laba = $this->db->query("SELECT case when date_format(a.tanggal,'%w') = 0 THEN 'MINGGU' when date_format(a.tanggal,'%w') = 1 THEN 'SENIN' when date_format(a.tanggal,'%w') = 2 THEN 'SELASA' when date_format(a.tanggal,'%w') = 3 THEN 'RABU' when date_format(a.tanggal,'%w') = 4 THEN 'KAMIS' when date_format(a.tanggal,'%w') = 5 THEN 'JUMAT' when date_format(a.tanggal,'%w') = 6 THEN 'SABTU' end as hari, a.tanggal, SUM(a.jumlah) AS jumlah_pem_total, SUM(b.jumlah) AS jumlah_pen_total, a.jumlah-b.jumlah AS laba, (((a.jumlah-b.jumlah)/b.jumlah)*100) AS persen_laba FROM tb_pemasukan a JOIN tb_pengeluaran b WHERE a.tanggal=b.tanggal GROUP BY a.tanggal")->result_array();
        $pem = $this->db->query("SELECT DAYNAME(tanggal) AS day, SUM(jumlah) AS total FROM tb_pemasukan GROUP BY DAY(tanggal)")->result_array();
        $pen = $this->db->query("SELECT DAYNAME(tanggal) AS day, SUM(jumlah) AS total FROM tb_pengeluaran GROUP BY DAY(tanggal)")->result_array();
        $tot_laba = $this->db->query("SELECT SUM(a.jumlah-b.jumlah) AS value FROM tb_pemasukan a JOIN tb_pengeluaran b WHERE a.tanggal=b.tanggal")->row_array();
        $laba_hari_ini = $this->db->query("SELECT SUM(a.jumlah-b.jumlah) AS value FROM tb_pemasukan a JOIN tb_pengeluaran b WHERE a.tanggal=b.tanggal AND a.tanggal='".$datenow."'")->row_array();
        $laba_kemarin = $this->db->query("SELECT SUM(a.jumlah-b.jumlah) AS value FROM tb_pemasukan a JOIN tb_pengeluaran b WHERE a.tanggal=b.tanggal AND a.tanggal='".$dateyes."'")->row_array();
        //$result = array();
        $data = [];
        foreach($pem as $pm) {
            /* foreach($pen as $pn){
                $data[] = array(
                    'hari' => $pm['day'],
                    'total' => $pm['total'],
                    'total_pengeluaran' => $pn['tot']
                );
                //array_push($result,$data);
            } */
            $hari = $pm['day'];
            $jumlah = $pm['total'];
        }
        foreach($pen as $pn) {
            /* foreach($pen as $pn){
                $data[] = array(
                    'hari' => $pm['day'],
                    'total' => $pm['total'],
                    'total_pengeluaran' => $pn['tot']
                );
                //array_push($result,$data);
            } */
            $hari2 = $pn['day'];
            $jumlah2 = $pn['total'];
        }

        //echo $jumlah2-$jumlah;
        //echo $hari;
        //echo json_encode($data);
        $data = array(
            'laba' => $laba,
            'data_laba' => array(
                'total_laba' => $tot_laba,
                'laba_hari_ini' => $laba_hari_ini,
                'laba_kemarin' => $laba_kemarin
            )
        );
        $this->response( $data, REST_Controller::HTTP_OK);
        //$this->response($res, 200);
    }

    function total_laba_get(){
        $tot_laba = $this->db->query("SELECT SUM(a.jumlah-b.jumlah) AS total_laba FROM tb_pemasukan a JOIN tb_pengeluaran b WHERE a.tanggal=b.tanggal")->result();
        $this->response( $tot_laba, REST_Controller::HTTP_OK);
    }

    //Masukan function selanjutnya disini
}
?>