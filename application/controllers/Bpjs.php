<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Rest Controller
 * A Complete Authentication using RESTful server implementation for CodeIgniter.
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Richard Sunny
 * @credits         Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/ricsunny/ciAuth
 * @version         3.1.3
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Bpjs extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('api_model');

        //Loading encryption class to encrypt and decrypt password
        $this->load->library('encryption');
        //Loading Email library
        $this->load->library('email');
    }

    public function login_post() {

        $email = $this->post('email');
        $password = $this->post('password');

        $encrypted_password = hash('sha512', $password);
        $login_user = $this->api_model->login($email, $encrypted_password);

        // print_r($login_user);

        if ($login_user != FALSE) {
            $is_active = $login_user[0]->is_active;
            if ($is_active != 0) {
                $api_token = $login_user[0]->api_token;
                // $no_rm = $login_user[0]->no_rm_pas;
                // $nama_pas = $login_user[0]->nama_pas;
                // $nohp = $login_user[0]->nohp;
                // $noKTP = $login_user[0]->ktp_pas;
                $message = array(
                    'response' => array("token" => $api_token),
                    'metadata' => array("message" => "Ok", "code" => 200)   
                );
            } else {
                $message = array(
                    'response' => array("token" => ""),
                    'metadata' => array("message" => "false", "code" => 5)
                );
            }
        } else {
            $message = array(
                'response' => array("token" => "false"),
                'metadata' => array("message" => "false", "code" => 404)
            );
        }

        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function getNoAntrian_post()
    {
        // $noRM = '005986';
        $noCard =  $this->post('nomorkartu');
        $nik =  $this->post('nik');
        $noRM =  $this->post('nomorrm');
        $noTlpn =  $this->post('notelp');
        $tglRes =  $this->post('tanggalperiksa');
        $userPoli =  $this->post('kodepoli');
        $noRefBpjs =  $this->post('nomorreferensi');
        $jnsRefBpjs =  $this->post('jenisreferensi');
        $jnsReqBpjs =  $this->post('jenisrequest');
        $poliEksBpjs =  $this->post('polieksekutif');
        $transMethod =  'BPJS';
        $uniq = date("Ymdhis");

        $tglAjah = substr($tglRes,0,-9);

        // $cekNoUrut = $this->api_model->getNoUrutTerakhir($tglRes); //cek apakah sudah ada nomor
        $cekPasien = $this->api_model->getCekPasien($nik,$noRM,$noTlpn); //cek sudah berapa kali daftar
        // print_r($cekPasien);

        $pasienName = $cekPasien[0]->nama_pas;
        $email = $cekPasien[0]->email;

        $cekDrName = $this->api_model->getNameDok($userPoli); //cek sudah berapa kali daftar
        // print_r($cekDrName);
        $drName = $cekDrName[0]->nmlengkap;
        
        $cekDebitur = $this->api_model->getCekDebitur($tglAjah,$noRM); //cek sudah berapa kali daftar
        // echo $cekDebitur;

        $message = $this->createNewReservasi($transMethod,$userPoli,$pasienName,$tglRes,$email,$noRM,$uniq,$drName);        
        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code

    }

     public function getRekapAntian_post()
    {
        
        $tglRes =  $this->post('tanggalperiksa');
        $userPoli =  $this->post('kodepoli');
        $poliEks =  $this->post('polieksekutif');


        if (!empty($data)) {
            $message = array(
                'response' => array("namapoli" => "true", "totalantrean" => "0000", "jumlahterlayani" => "", "lastupdate" => "","lastupdatetanggal" => "", ), 
                'code' => array("message" => "Ok", "code" => "200"));
        } else {
            $message = array(
                'response' => array("token" => "false"),
                'metadata' => array("message" => "false", "code" => 404)
            );
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function kodeBookOperasi_post()
    {
        $noOprs =  $this->post('nopeserta');

        if (!empty($data)) {
            $message = array(
                'response' => array(
                    'list' => array(
                        "kodebooking"=>"",
                        "tanggaloperasi"=>"",
                        "jenistindakan"=>"",
                        "kodepoli"=>"",
                        "namapoli"=>"",
                        "terlaksana"=>"",
                    ) ), 
                'code' => array("message" => "Ok", "code" => "200"));
        } else {
            $message = array(
                'response' => array("token" => "false"),
                'metadata' => array("message" => "false", "code" => 404)
            );
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function listJdwOperasi_post()
    {
        $noOprs =  $this->post('nopeserta');

        if (!empty($data)) {
            $message = array(
                'response' => array(
                    'list' => array(
                        $data
                    ) ), 
                'code' => array("message" => "Ok", "code" => "200"));
        } else {
            $message = array(
                'response' => array("token" => "false"),
                'metadata' => array("message" => "false", "code" => 404)
            );
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function createNewReservasi($transMethod,$userPoli,$pasienName,$tglRes,$email,$noRM,$uniq,$drName)
    {
        $setAntrian = $this->createAntrian($transMethod,$userPoli,$pasienName,$tglRes);
        $noUrutPes = $setAntrian;

        // echo $noUrutPes;

        $getNoIdUser = $this->api_model->getUserDetail($email);

        if (!empty($getNoIdUser)) {
            $idUser = $getNoIdUser[0]->id_login;
            $nohp = $getNoIdUser[0]->nohp;

            $dataReg = array(
                'no_rm_pas_reg' => $noRM, 
                'no_urut_pas' => $noUrutPes, 
                'nama_penanggung_reg' => $pasienName, 
                'no_penanggung_reg' => $nohp, 
                'alamat_penanggung_reg' => '', 
                'cara_datang' => 'Datang Sendiri', 
                'hubungan_penganggung_reg' => '-', 
                'detail_cara_datang' => '-', 
                'id_antrian_khusus' => '0', 
                'debitur_reg_utama' => $transMethod, 
                'unicode_reg' => $uniq,
                'tgl_reg_utama' => $tglRes, 
                'id_user_reg' => $idUser, 
                'pasien_diantar_ambulance' => '', 
                'online_reg' => 1, 
            );

            $datainsert00 = $this->db->insert('tb_register', $dataReg);

            $dataMeta = array(
                'no_rm_pas_reg_meta' => $noRM, 
                'tgl_periksa_meta' => $tglRes, 
                'debitur_reg_meta' => $transMethod, 
                'dokter_reg_meta' => $drName,  //idDokter
                'id_pol_reg' => $userPoli, //idPoli 
                'unicode_reg_meta' => $uniq, 
                'type_pendaftaran' => 'pendaftaran', 
                'paket_registrasi_dipilih_idtind' => '0', 
                'id_user_regmeta' => $idUser, 
            );
            // print_r($dataMeta);
            $dataInsert = $this->db->insert('tb_register_meta', $dataMeta);
        
            if (!empty($dataInsert)) {
                $message = array(
                    'response' => array("nomorantrean" => "", "kodebooking" => "0000", "jenisantrean" => "", "estimasidilayani" => "","namapoli" => ""), 
                    'metadata' => array("message" => 'Ok',"code" => '200' ));
                return $message; 
                } else {
                $message = array(
                    'response' => array("nomorantrean" => "null", "kodebooking" => "null", "jenisantrean" => "null", "estimasidilayani" => "null","namapoli" => "null"), 
                    'metadata' => array("message" => 'False',"code" => '404'));
                return $message; 
            
            }

        } else {
            $message = array(
                'response' => array("nomorantrean" => "null", "kodebooking" => "null", "jenisantrean" => "null", "estimasidilayani" => "null","namapoli" => "null"), 
                'metadata' => array("message" => 'False',"code" => '404'));
            return $message; 
        }
    }

    public function createAntrian($transMethod,$userPoli,$pasienName,$tglRes)
    {
        $gshfga = "Pasien Umum";
        if($transMethod == "BPJS"){
            $gshfga = "Pasien BPJS";
        }
        $ggtsaaa = "select count(id_met) as xxx from tb_antrian_meta  where id_ins='".$userPoli."' and tglmsk like '%".date("Y-m-d")."%' AND  dipanggil_oleh='0' ";
        $gghsgba = $this->db->query($ggtsaaa);
        $fgfhjsl = $gghsgba->row();
        $tunggu = 0;
        if($fgfhjsl){
            $tunggu = $fgfhjsl->xxx;
        }
        //PERTAMA ADALAH AMBIL SEMUA DATANYA YA
        $this->db->where('id_ins', $userPoli);
        $this->db->limit('1');
        $rt = $this->db->get('tb_instalasi');
        $sr = $rt->result();	
        $nmpl = "POLIKLINIK ". strtoupper($sr[0]->nm_instalasi);
            $this->db->select('urutan');
            $this->db->where('id_ins', $sr[0]->id_ins);
            $this->db->like('tglmsk', date("Y-m-d", strtotime($tglRes)));
            $this->db->order_by('id_met', 'DESC');
            $this->db->limit('1');
            $urut  = $this->db->get('tb_antrian_meta');
            $urut1 = $urut->result();
        //nah selanjutnya ambil nomor antrian nah buat default antrian pertama
        $antrianpertama = 0;
        if($urut1){
            $antrianpertama = $urut1[0]->urutan;
        }
        $urutlanjut = $antrianpertama+1;
        //gadipake yaaaa
        //$newkdurut  = strtoupper($sr[0]->awalan) . sprintf("%03s", $urutlanjut);
        $newkdurut  = strtoupper($sr[0]->awalan) . $urutlanjut;
        //selanjutnya adalah masukkan kedalam tabel antrian meta
        $dtInsert = array(
            'id_ins' => $sr[0]->id_ins,
            'awalan' => $sr[0]->awalan,
            'type' => 'pendaftaran',
            'urutan' => $urutlanjut,
            'kdurutan' => $newkdurut,
            'tglmsk' => $tglRes,
            'ket_penjamin_khusus' => $pasienName,
        );
        $apsjjj = strtoupper($sr[0]->awalan) ." <span style='margin:0 0 0 10px;'>".  $urutlanjut ."</span>";
        $this->db->insert('tb_antrian_meta', $dtInsert);

        return $newkdurut;

    }

}

?>