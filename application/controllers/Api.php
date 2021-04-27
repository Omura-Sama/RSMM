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

class Api extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
        $this->load->model('api_model');

        //Loading encryption class to encrypt and decrypt password
        $this->load->library('encryption');
        //Loading Email library
        $this->load->library('email');
    }

        public function verifikasiEmail_post()
    {
        $email = $this->post('email');
        $password = $this->post('password');
        $nik = $this->post('nik');

        define('ROOT', 'http://izeber.xyz/index.php/api/');
        $kode   = md5(uniqid(rand()));
        $dataVery = array(
                'nik' => $nik, 
                'email' => $email, 
                'codes' => $kode,
                'password' => $password
            );

        // print_r($dataVery);
        $datainsertVery = $this->db->insert('verifikasi_email', $dataVery);

        // $stream = imap_open("{mail.rsmm-indramayu.id:143}INBOX.Drafts", "pendaftaran.online@rsmm-indramayu.id", "rsmmonline21@");
        // $mails = imap_search($stream, 'UNSEEN');
        // rsort($mails);
        //     foreach ($mails as $mailId) {
        //     imap_fetch_overview($stream, $mailId, 0);
        // } //that was the mistake when email number is too big!
        $to_address = ".$email.";       
        $from_address = "pendaftaran.online@rsmm-indramayu.id";
        $subject = "Hallo, ".$email." 
                    Terima kasih telah mendaftar sebagai calon pasien di RSMM Indramayu.
                    Silahkan klik tombol dibawah ini untuk mengaktifkan akun anda.
                    Email           : ".$email."
                    Link Aktivasi   : ".ROOT."verifikasiEmail?kode=$kode
                    ";

        // $subject = "
        //     <html>
        //         <head>
        //         <title>Registrasi User Online</title>
        //         </head>
        //     <body>
        //         <p>Hallo
        //             Terima kasih telah mendaftar sebagai calon pasien di RSMM Indramayu.
        //             Silahkan klik tombol dibawah ini untuk mengaktifkan akun anda.
        //         </p>
            
        //         <table border='2px'>
        //             <tr>
        //                 <th>Username</th>
        //                 <th>Confirmation</th>
        //             </tr>
        //             <tr>
        //                 <td>".$email."</td>
        //                 <td>
        //                     <button 
        //                         type='button' 
        //                         herf='".ROOT."verifikasiEmail?kode=$kode'
        //                         style='background:#00ab4e;padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px;color:#fff'>Verifikasi Email Saya
	    //                     </button>
        //                     <br>
        //                     Jika link diatas tidak dapat diklik, silahkan salin tautan dibawah ini ke browser anda.
        //                     <br>
        //                     ".ROOT."verifikasiEmail?kode=$kode
        //                 </td>
        //             </tr>
        //         </table>
        //     </body>
        //     </html>
            // ";
         
        //Sending a mail
        $stream =  imap_mail($to_address, $from_address, $subject);
 
        if($stream AND $datainsertVery)
        {
            // echo '<p class="info">Berhasil Dikirim</p>';
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Successfully send an user account"),
                'body' => array()
            );
        }
        else
        {
            // echo '<p class="infoGagal">Gagal Dikirim</p>';
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to send an email of new user account"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code 
    }

    public function verifikasiEmail_get()
    {
        $kode = $this->get('kode');
        $dataKode = $this->api_model->getKodeverifikasi($kode);
        // print_r($dataKode);

        $email = $dataKode[0]->email;
        $nik = $dataKode[0]->nik;
        $editUser = array(
            'is_active' => 1
        );
        $this->db->where('nik', $nik);
        $this->db->where('email', $email);
        $data = $this->db->update('tb_login', $editUser); 

        if ($data) {
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Berhasil Verifikasi"),
                'body' => array()
            );
        }else{
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Gagal Verifikasi"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code 
    }

    public function forgetPassword_post()
    {
        $email = $this->post('email');
        $password = $this->post('password');
        $nik = $this->post('nik');
        
        define('ROOTP', 'http://izeber.xyz/index.php/api/');

        $to_address = ".$email.";       
        $from_address = "pendaftaran.online@rsmm-indramayu.id";
        $subject = "Hallo, ".$email." 
                    Terima kasih telah melakukan reset password.
                    Silahkan klik tombol dibawah ini untuk mengubah password akun anda.
                    Email           : ".$email."
                    Link Aktivasi   : ".ROOTP."forgetPassword?nik=$nik&email=$email
                    ";
        $kirim =  imap_mail($to_address, $from_address, $subject); 
        if($kirim)
        {
            // echo '<p class="info">Berhasil Dikirim</p>';
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Successfully send an user account"),
                'body' => array()
            );
        }
        else
        {
            // echo '<p class="infoGagal">Gagal Dikirim</p>';
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to send an email of new user account"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code 
    }

    public function forgetPassword_get()
    {
        $nik = $this->get('nik');
        $email = $this->get('email');
        // $dataKode = $this->api_model->getKodeverifikasi($kode);
        $data['nik'] = $nik;
		$data['email'] =  $email;

		$this->load->view('reset_password',$data);
    }

    public function login_post() {

        $email = $this->post('username');
        $password = $this->post('password');

        $encrypted_password = hash('sha512', $password);
        $login_user = $this->api_model->login($email, $encrypted_password);

        // print_r($login_user);

        if ($login_user != FALSE) {
            $is_active = $login_user[0]->is_active;
            if ($is_active != 0) {
                $api_token = $login_user[0]->api_token;
                $no_rm = $login_user[0]->no_rm_pas;
                $nama_pas = $login_user[0]->nama_pas;
                $nohp = $login_user[0]->nohp;
                $noKTP = $login_user[0]->ktp_pas;
                $message = array(
                    'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "User logged in Successfully"),
                    'body' => array("apiToken" => $api_token, "no_rm_pas" => $no_rm, "no_ktp_pas" => $noKTP, "nama_pas" => $nama_pas, "nohp" =>$nohp)   
                );
            } else {
                $message = array(
                    'header' => array("result" => "false", "resultCode" => "0005", "resultMessage" => "You need to verify your email in order to login."),
                    'body' => array("apiToken" => "", "no_rm_pas" => "", "no_ktp_pas" => "", "nama_pas" => "", "nohp" => "")
                );
            }
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "You've entered incorrect email or password."),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function tiket_get()
    {
        $norRM = $this->get('norm');
        // echo $norRM;
        $data = $this->api_model->get_registration($norRM);

        // print_r($data);

        $poliId = $data[0]->id_pol_reg;
        $dokterId = $data[0]->dokter_reg_meta;
        $uniCode = $data[0]->unicode_reg;
        $getTglReg = $data[0]->tgl_reg_utama;
        $tglReg =  date("Y-m-d", strtotime($getTglReg));
       
        // $noCurt = $data[0]->no_urut_pas;

        // echo $poliId;
        // echo "<br>";
        // echo $dokterId;
        // echo "<br>";
        // echo $tglReg;

        $dataHS = $this->api_model->get_HSregistration($norRM,$uniCode);
        $data1 = $this->api_model->get_pervRegistration($poliId,$dokterId,$tglReg);

        // print_r($data1);

        $dataJoin = array_push($data, $dataHS);

        if ($data1 == false) {
            $data1 = array(
                'no_urut_pas' => '', 
                'Tgl_reg_utama' => '' 
            );
        }

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $dataHS,"now" => $data1));
        } else {
            $dataNull = array(
                'no_urut_pas' => '', 
                'Tgl_reg_utama' => '' 
            );
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => $dataNull));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);

    }

    public function uploadImg_post()
    {
        $noRM =  $this->post('noRM');
        $insImg = $this->input->post('imgKet');

        if ($insImg == 'ktp') {
            $imgSet = $this->input->post('ktpImg');
           
        }elseif ($insImg == 'kk') {
            $imgSet = $this->input->post('kkImg');
            
        }elseif ($insImg == 'bpjs') {
            $imgSet = $this->input->post('bpjsImg');
        }

        // file_put_contents('./upload', $data);
        define('UPLOAD_DIR', 'upload/');
        $img = $imgSet;
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = UPLOAD_DIR . uniqid() . '.png';
        $success = file_put_contents($file, $data);
        // print $success ? $file : 'Unable to save the file.';
       
        $cekPasien = $this->api_model->get_DokKelengkapan($noRM);

        if ($cekPasien) {
            if ($insImg == 'ktp') {
                $editDoc = array(
                    'no_rm_pas' => $noRM,
                    'ktp_img'   => $file
                );
            
            }elseif ($insImg == 'kk') {
                // $imgSet = $this->input->post('kkImg');
                $editDoc = array(
                    'no_rm_pas' => $noRM,
                    'kk_img'   => $file
                );
                
            }elseif ($insImg == 'bpjs') {
                // $imgSet = $this->input->post('bpjsImg');
                $editDoc = array(
                    'no_rm_pas' => $noRM,
                    'bpjs_img'   => $file
                );
            }

            $this->db->where('no_rm_pas', $noRM);
            $data = $this->db->update('tb_dok_kelengkapan', $editDoc); 
        }else{
             
            if ($insImg == 'ktp') {
                $dtInsert = array(
                    'no_rm_pas' => $noRM,
                    'ktp_img' => $file,
                    'bpjs_img' => 'null',
                    'kk_img' => 'null'
                );
            
            }elseif ($insImg == 'kk') {
                // $imgSet = $this->input->post('kkImg');
                
                $dtInsert = array(
                    'no_rm_pas' => $noRM,
                    'ktp_img' => 'null',
                    'bpjs_img' => 'null',
                    'kk_img' => $file
                );
                
            }elseif ($insImg == 'bpjs') {
                // $imgSet = $this->input->post('bpjsImg');
                
                 $dtInsert = array(
                    'no_rm_pas' => $noRM,
                    'ktp_img' => 'null',
                    'bpjs_img' => $file,
                    'kk_img' => 'null'
                );
            }
            $data = $this->db->insert('tb_dok_kelengkapan', $dtInsert);
        }

        if (!empty($data)) {        
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $data, "dataUpload" => $data));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => "Null"));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);

    }

    // public function resetPassword_get()
    // {   
    //     $newpass = $this->post('newpass');
    //     $confpass = $this->post('confirmpass');

    //     // $email = $dataKode[0]->email;
    //     // $nik = $dataKode[0]->nik;
    //     // $editUser = array(
    //     //     'is_active' => 1
    //     // );
    //     // $this->db->where('nik', $nik);
    //     // $this->db->where('email', $email);
    //     // $data = $this->db->update('tb_login', $editUser); 

    //     // if ($data) {
    //     //     $message = array(
    //     //         'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Berhasil Verifikasi"),
    //     //         'body' => array()
    //     //     );
    //     // }else{
    //     //     $message = array(
    //     //         'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Gagal Verifikasi"),
    //     //         'body' => array()
    //     //     );
    //     // }

    //     // $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code 
    // }

    public function editProfil_post()
    {
        $noRM =  $this->post('noRM');
        $noKTP =  $this->post('no_ktp');
        $pasienName =  $this->post('nama_pas');
        $tmptLahir =  $this->post('tmpt_lahir');
        $tglLahir =  $this->post('tgl_lahir');
        // $desa =  $this->post('desa');
        $agama =  $this->post('agama');
        $golDar =  $this->post('gol_darah');
        $noTlp =  $this->post('no_tlp');
        $work =  $this->post('work');
        $edu =  $this->post('edu');
        $statNikah =  $this->post('stat_nikah');
        $suami =  $this->post('orangtua');
        $jenKel =  $this->post('jenis_kelamin');
        $address =  $this->post('address');
        //  $this->input->post(file_get_contents($_FILES['imgProfile']['tmp_name']));

        // $ktpImge = $this->upload->do_upload('ktpImg'); 
        // $bpjsImge =  $this->upload->do_upload('bpjsImg');
        // $kkImge = $this->upload->do_upload('kkImg');
        

        $editData = array(
                    'no_rm_pas'       => $noRM,
                    'nama_pas'          => $pasienName,
                    'ktp_pas'    => $noKTP,
                    'jenkel_pas'    => $jenKel,
                    'tmpt_lhr_pas'    => $tmptLahir,
                    'tgl_lhr_pas'    => $tglLahir,
                    'status_pas'    => $statNikah,
                    'agama_pas'    => $agama,
                    'goldar_pas'    => $golDar,
                    'pekerjaan_pas'    => $work,
                    'alamat_pas'    => $address,
                    'telp_pas'    => $noTlp,
                    'pendidikan_pas'    => $edu,
                    'orangtua_pas'    => $suami
                    // ,
                    // 'id_desa'    => $noKTP
                );
        $this->db->where('no_rm_pas', $noRM);
        $data = $this->db->update('tb_pasien', $editData);

        // $this->load->library('upload');
        // $this->upload->initialize($config);
        // $image = base64_decode($this->upload->do_upload('ktpImg'));
        
        //PERTAMA ADALAH AMBIL SEMUA DATANYA YA
        // $this->db->where('no_rm_pas', $noRM);
        // $this->db->limit('1');
        // $cekRMdoc = $this->db->get('tb_dok_kelengkapan');
        // $tesDoc = $cekRMdoc->result();

        // $new_name = time().$_FILES["userfiles"]['name'];    

    
        // if ($tesDoc != '') {
        //     $dataKtpImg = array('upload_dataKtp' => $this->upload->data());
        //     $path = $config['upload_path'].'/'.$data['upload_dataKtp']['orig_name'];
        //     $editDoc = array(
        //             'no_rm_pas' => $noRM,
        //             'ktp_img'   => $path
        //             // ,
        //             // 'bpjs_img'  => $bpjsImge,
        //             // 'kk_img'    => $kkImge
        //             // ,
        //             // 'id_desa'    => $noKTP
        //         );
        //     $this->db->where('no_rm_pas', $noRM);
        //     $dataImg = $this->db->update('tb_dok_kelengkapan', $editDoc);

        //     echo 'insert';
        // }else{
        //     $dataImg = '';
        //     echo 'No insert';
        // }

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $data));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => "Null"));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);

    }

    public function reservasi_post()
    {
        // $noRM = '005986';
        $noRM =  $this->post('noRM');
        $pasienName =  $this->post('nama_pas');
        $drName =  $this->post('drName');
        $userPoli =  $this->post('userPoli');
        $transMethod =  $this->post('transMethod');
        $email =  $this->post('email');
        $tglRes =  $this->post('tglReservasi');
        $uniq = date("Ymdhis");

        // echo $tglRes;

        $tglAjah = substr($tglRes,0,-9);

        // $cekNoUrut = $this->api_model->getNoUrutTerakhir($tglRes); //cek apakah sudah ada nomor
        $cekDebitur = $this->api_model->getCekDebitur($tglAjah,$noRM); //cek sudah berapa kali daftar

        // echo $cekDebitur;

        if ($cekDebitur == 3 ) {
            if ($cekDebitur >= 3) {
                $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Tidak bisa daftar 3 kali"), 'body' => array("data" => "Null"));
                $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
            }else{
                // echo 'lanjut';
                $message = $this->createNewReservasi($transMethod,$userPoli,$pasienName,$tglRes,$email,$noRM,$uniq,$drName);
            }
        }else{
            // echo "test";
            // print_r($cekDebitur);
        // }
            if ($cekDebitur == 2 || $cekDebitur == 1 || $cekDebitur == 0) {
                $message = $this->createNewReservasi($transMethod,$userPoli,$pasienName,$tglRes,$email,$noRM,$uniq,$drName);
            }
            else{
            // print_r($cekDebitur);

                if ($cekDebitur[0]->debitur_reg_utama == 'BPJS') {
                    $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Tidak bisa daftar 2 kali"), 'body' => array("data" => "Null"));
                    $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                }elseif ($cekDebitur[0]->debitur_reg_utama == 'UMUM') {
                // echo 'lanjut';
                    $message = $this->createNewReservasi($transMethod,$userPoli,$pasienName,$tglRes,$email,$noRM,$uniq,$drName);
                }
            }
        }
        // if (empty($cekNoUrut)) {
        //     $noUrutPes = 'ON-01';
        // }else{
        //     $noSet = $cekNoUrut[0]->no_urut_pas;
        //     $noGet = substr($noSet, -2);
        //     $noGetplus1 = $noGet+1;

        //     if ($noGet < 9) {
        //         $noUrutPes = 'ON-0'.$noGetplus1;
        //     }else{
        //         $noUrutPes = 'ON-'.$noGetplus1;
        //     }   
        // }    
        
        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code

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
                $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is success saved"), 'body' => array("data" => 'Berhasil di tambah'));
                return $message; 
                } else {
                $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => "Null"));
                return $message; 
            
            }

        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "email not exist"), 'body' => array("data" => "Null"));
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

    public function listDokter_get()
    {
        $data = $this->api_model->get_list_dokter();

        // print_r($data);       

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $data));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => "Null"));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function listPoli_get()
    {
        $data = $this->api_model->get_list_poli();

        // print_r($data);       

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $data));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => "Null"));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function jadok_post()
    {
        $searching = $this->post('scrjadok');
        // echo $searching;

        if (isset($searching)) {
            $data = $this->api_model->get_schjadwal_dokter($searching);        
        }else{
            $data = $this->api_model->get_jadwal_dokter();
        }

        // print_r($data);

        foreach ($data as $key ) {
            $hari = $key->id_hari;
            $namaInst = $key->nm_instalasi;
            $idDok = $key->id_dok;
            $nmDok = $key->nmlengkap;
            $jamMulai = $key->jam_mulai;
            $jamSls = $key->jam_selesai;

            $cekLastUrutan = $this->api_model->get_sisa_quota($idDok);        


            if ($hari == '1') {
                $hariC = 'senin';
            }elseif($hari == '2'){
                $hariC = 'selasa';
            }elseif($hari == '3'){
                $hariC = 'rabu';
            }elseif($hari == '4'){
                $hariC = 'kamis';
            }elseif($hari == '5'){
                $hariC = 'jumat';
            }elseif($hari == '6'){
                $hariC = 'sabtu';
            }

            $convertHari[] = array('id_hari' => $hariC, 'nm_instalasi' => $namaInst, 'nmlengkap' => $nmDok, 'jam_mulai' => $jamMulai, 'jam_selesai' => $jamSls );
        }

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $convertHari));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => $convertHari));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
        
    }

    
      public function jadok_get()
    {
         $searching = $this->get('scrjadok');
        // echo $searching;

        if (isset($searching)) {
            $data = $this->api_model->get_schjadwal_dokter($searching);        
        }else{
            $data = $this->api_model->get_jadwal_dokter();
        }
        // print_r($data);

        foreach ($data as $key ) {
            $hari = $key->id_hari;
            $namaInst = $key->nm_instalasi;
            $nmDok = $key->nmlengkap;
            $jamMulai = $key->jam_mulai;
            $jamSls = $key->jam_selesai;
            $kuotaPasien = $key->kuota_pasien_online;

            if ($hari == '1') {
                $hariC = 'senin';
            }elseif($hari == '2'){
                $hariC = 'selasa';
            }elseif($hari == '3'){
                $hariC = 'rabu';
            }elseif($hari == '4'){
                $hariC = 'kamis';
            }elseif($hari == '5'){
                $hariC = 'jumat';
            }elseif($hari == '6'){
                $hariC = 'sabtu';
            }
            $convertHari[] = array('id_hari' => $hariC, 'nm_instalasi' => $namaInst, 'nmlengkap' => $nmDok, 'kuotaOnline' => $kuotaPasien, 'jam_mulai' => $jamMulai, 'jam_selesai' => $jamSls );

            // $convertHari[] = array('id_hari' => $hariC, 'nm_instalasi' => $namaInst, 'nmlengkap' => $nmDok );
        }        

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $convertHari));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => "Null"));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
        
    }

    public function jadop_post()
    {
        $data = $this->api_model->get_jadwal_operasi();
        // print_r($data);

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $data));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => $data));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }



    public function jadop_get()
    {
        $searching = $this->get('scrjadop');

        // echo $searching;
        
        if (isset($searching)) {
            $data = $this->api_model->get_schjadwal_operasi($searching);        
        }else{
            $data = $this->api_model->get_jadwal_operasi();
        }

        // print_r($data);

        if (!empty($data)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "data is verified"), 'body' => array("data" => $data));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "data not in parameters"), 'body' => array("data" => 'Data Tidak Ditemukan'));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }

    public function forgot_password_post() {

        $email = $this->post('email');
        $email_exists = $this->api_model->check_email_exists($email);

        if ($email_exists != FALSE) {
            //Update password requested time
            $this->api_model->update_password_requested_time($email);
            //Encode user email to attach to the url 
            $hash_email = md5($email);
            $url = base_url() . "welcome/change_password/" . $hash_email;
            //Send email with credentials to user
            $this->email->from('info@domain.com', 'App Name');
            $this->email->to($email);
            $this->email->subject('Password Change Request');
            $this->email->message('Dear Customer,
							 <br/><br/>
							 Please click on the following LINK to create your new password:<br/>
							 <a href="' . $url . '">Change Password</a><br/>
                                                         <br/><br/>
							 Best Regards,<br/>');
            $this->email->send();
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Email with url to get password sent Successfully."),
                'body' => array("status" => "true")
            );
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "Email Record does not exist"),
                'body' => array("status" => "false")
            );
        }
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function change_password_post() {
        $apiToken = $this->post('apiToken');
        $oldPassword = $this->post('oldPassword');
        $encryptedOldPassword = hash('sha512', $oldPassword);

        $newPassword = $this->post('newPassword');
        $encryptedNewPassword = hash('sha512', $newPassword);
        //Match the entered oldPassword password with stored password
        $password_check = $this->api_model->match_old_password($apiToken, $encryptedOldPassword);
        if ($password_check == TRUE) {
            $modify_password = $this->api_model->modify_forgot_password($apiToken, $encryptedNewPassword);

            if ($modify_password == 1) {
                $message = array(
                    'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Password Updated Successfully"),
                    'body' => array()
                );
            } else {
                $message = array(
                    'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to update your new password"),
                    'body' => array()
                );
            }
        } else {

            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "Your entered password did not match your old password"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }
    
    public function update_password_post() {

        $remember_token = $this->input->post('hash_email');
        $new_password = $this->input->post('new_password');

        $confirm_password = $this->input->post('confirm_password');

        if ($new_password != $confirm_password) {

            $this->session->set_flashdata('message', 'You New Password & Confirm Password does not match.');

            redirect('welcome/change_password/' . $remember_token);
        } else {

            //Encrypt new password

            $encrypted_password = hash('sha512', $new_password);

            //Check if the url is expired or not (password_requested_time is less than 1 hour )
            //Get password requested time saved in db

            $password_requested_time = $this->api_model->get_password_requested_time($remember_token);

            if ($password_requested_time != FALSE) {

                $request_time = $password_requested_time[0]->password_requested_time;

                //Get current date time

                $current_time = date('Y-m-d H:i:s');

                $diff = strtotime($current_time) - strtotime($request_time);

                $diff_in_hrs = $diff / 3600;

                if ($diff_in_hrs < 1) {

                    $save_user_password = $this->api_model->save_new_password($remember_token, $encrypted_password);

                    if ($save_user_password != FALSE) {

                        $data['message'] = "Your Password has been updated. Please login using your new password in the Mobile App. ";

                        $this->load->view('message', $data);
                    } else {

                        $data['message'] = "Sorry! We are unable to update your password this time.";

                        $this->load->view('message', $data);
                    }
                } else {

                    $data['message'] = "Your Password Change Link has been expired. Please start the password change process again.";

                    $this->load->view('message', $data);
                }
            } else {

                $data['message'] = "It seems you've already updated or have not requested a password change. Please start the password change process again.";

                $this->load->view('message', $data);
            }
        }
    }

    public function change_password_without_old_post() {

        $email = $this->post('email');
        $newPassword = $this->post('newPassword');
        $encryptedNewPassword = hash('sha512', $newPassword);
        //Match the entered oldPassword password with stored password
        $modify_password = $this->api_model->modify_forgot_password_by_email($email, $encryptedNewPassword);

        if ($modify_password == 1) {
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "Password Updated Successfully"),
                'body' => array()
            );
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to update your new password"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

     public function logout_post(){
        //delete all session
        // session_destroy();

        $email = $this->post('email');
        $password = $this->post('password');

        $encrypted_password = hash('sha512', $password);
        $login_user = $this->api_model->login($email, $encrypted_password);

        $api_token = $login_user[0]->api_token;
        $message = array(
                    'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "User logged in Successfully"),
                    'body' => array("apiToken" => $api_token)   
                );
        // $this->output->set_output(json_encode(array('status'=>true,'msg'=>'log Out successfully')));
        $this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

    }

    public function logged_me_in_post() {
        $authToken = $this->post('apiToken');
        $email = $this->api_model->getEmailByAuthToken($authToken);
        if (!empty($email)) {
            $message = array('header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "email is verified"), 'body' => array("vemail" => $email));
        } else {
            $message = array('header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "email not in parameters"), 'body' => array("vemail" => $email));
        }
        $this->set_response($message, REST_Controller::HTTP_CREATED);
    }


}

?>