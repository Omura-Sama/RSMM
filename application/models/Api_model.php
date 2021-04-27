<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter Model
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
class Api_model extends CI_Model {

    var $Users = 'tb_login';
    var $Rsmm = 'tb_user';
    var $Pasien = 'tb_pasien';
    var $regisPoli = 'tb_register';
    var $kartuBpjs = 'tb_pasien_no_kartu';

    function __construct() {    
        // Call the Model constructor
        parent::__construct();
         $this->load->database();
    }

    public function getCekPasien($nik,$noRM,$noTlpn)
    {
        $this->db->select('*');
        $this->db->from($this->Pasien);
        // $this->db->from('tb_login');
        $this->db->join('tb_login', 'tb_pasien.ktp_pas = tb_login.nik','left');
        // $this->db->where('tb_pasien.ktp_pas', $nik);
        $this->db->where('tb_login.nik', $nik);
        $this->db->where('no_rm_pas', $noRM);
        $this->db->where('tb_login.nohp', $noTlpn);
        // $this->db->where('telp_pas', $noTlpn);
        $query = $this->db->get(); 
        
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getNameDok($userPoli)
    {
        $this->db->select('tb_user.nmlengkap');
        $this->db->from('tb_register');
        $this->db->join('tb_register_meta', 'tb_register.unicode_reg = tb_register_meta.unicode_reg_meta','left');
        $this->db->join('tb_instalasi', 'tb_register_meta.id_pol_reg = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'tb_register_meta.dokter_reg_meta = tb_user.id_user','left');
        $this->db->where('id_pol_reg', $userPoli);
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getNoUrutTerakhir($tglRes)
    {
        $this->db->select('no_urut_pas');
        $this->db->from($this->regisPoli);
        // $this->db->like('tgl_reg_utama', date('Y-m-d'));
        $this->db->like('tgl_reg_utama', $tglRes);
        $this->db->like('no_urut_pas', 'ON-');
        $this->db->order_by('tgl_reg_utama', 'DESC');
        $this->db->order_by('no_urut_pas', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get(); 
        
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }

    }

    public function getKodeverifikasi($kode)
    {
        $this->db->select('email,nik');
        $this->db->from('verifikasi_email');
        $this->db->where('codes', $kode);
        $query = $this->db->get(); 
        
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getCekDebitur($tglRes,$noRM)
    {
        $this->db->select('debitur_reg_utama');
        $this->db->from($this->regisPoli);
        // $this->db->like('tgl_reg_utama', date('Y-m-d'));
        $this->db->where('no_rm_pas_reg', $noRM);
        $this->db->like('tgl_reg_utama', $tglRes);
        // $this->db->like('no_urut_pas', 'ON-');
        $this->db->order_by('tgl_reg_utama', 'DESC');
        $this->db->order_by('no_urut_pas', 'DESC');
        $this->db->limit(3);
        $query = $this->db->get(); 
        
        if ($query->num_rows() == 1) {
            return $query->result();
        }elseif ($query->num_rows() == 3) {
           return $query->num_rows();
        } else {
           return $query->num_rows();
            // return false;
        }
    }

    public function get_registration($norRM)
    {
        $this->db->select('no_urut_pas, unicode_reg, id_pol_reg, dokter_reg_meta, tgl_reg_utama, debitur_reg_utama, nm_instalasi ');
        $this->db->from('tb_register');
        $this->db->join('tb_register_meta', 'tb_register.no_rm_pas_reg = tb_register_meta.no_rm_pas_reg_meta','left');
        $this->db->join('tb_instalasi', 'tb_register_meta.id_pol_reg = tb_instalasi.id_ins','left');
        $this->db->where('no_rm_pas_reg', $norRM);
        $this->db->order_by('tgl_reg_utama', 'DESC');
        $this->db->order_by('tgl_periksa_meta', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function get_DokKelengkapan($noRM)
    {
        $this->db->select('*');
        $this->db->from('tb_dok_kelengkapan');
        $this->db->where('no_rm_pas', $noRM);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function get_HSregistration($norRM,$uniCode)
    {
        $this->db->select('nama_penanggung_reg, no_urut_pas, id_pol_reg, dokter_reg_meta, tgl_reg_utama, debitur_reg_utama, nm_instalasi ');
        $this->db->from('tb_register');
        // $this->db->join('tb_register_meta', 'tb_register.no_rm_pas_reg = tb_register_meta.no_rm_pas_reg_meta','left');
        $this->db->join('tb_register_meta', 'tb_register.unicode_reg = tb_register_meta.unicode_reg_meta','left');
        $this->db->join('tb_instalasi', 'tb_register_meta.id_pol_reg = tb_instalasi.id_ins','left');
        // $this->db->where('no_rm_pas_reg', $norRM);
        $this->db->where('no_rm_pas_reg_meta', $norRM);
        $this->db->where('tb_register.unicode_reg !=', $uniCode);
        $this->db->where('tb_register_meta.unicode_reg_meta !=', $uniCode);
        // $this->db->where('dokter_reg_meta !=', $dokterId);
        // $this->db->where('apakah_mulai_pemeriksaan', 'Y');
        $this->db->order_by('tgl_reg_utama', 'DESC');
        $this->db->order_by('tgl_periksa_meta', 'DESC');     
        // $this->db->limit(1);
        $query = $this->db->get();
        if (!empty($query->num_rows())) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function get_pervRegistration($poliId,$dokterId,$tglReg)
    {
        $this->db->select('no_urut_pas, tgl_reg_utama, nmlengkap, debitur_reg_utama, nm_instalasi ');
        $this->db->from('tb_register');
        $this->db->join('tb_register_meta', 'tb_register.unicode_reg = tb_register_meta.unicode_reg_meta','left');
        $this->db->join('tb_instalasi', 'tb_register_meta.id_pol_reg = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'tb_register_meta.dokter_reg_meta = tb_user.id_user','left');
        $this->db->where('id_pol_reg', $poliId);
        $this->db->where('dokter_reg_meta', $dokterId);
        $this->db->where('apakah_mulai_pemeriksaan', 'Y');
        $this->db->like('tgl_reg_utama', $tglReg);
        $this->db->order_by('tgl_reg_utama', 'DESC');
        // $this->db->order_by('tgl_periksa_meta', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function create_new_userbpjs($email, $nik, $nohp, $noBpjs, $encrypted_password, $name, $api_token, $remember_token) {

        $password = $encrypted_password;

        //Query to check if the email already exists in tb_login 
        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('nik', $nik);
        $this->db->limit(1);
        $query = $this->db->get();     
        
            // $this->db->select_max('no_rm_pas');
            // $this->db->from('tb_pasien');
            // $getMax = $this->db->get();  

            // print_r($getMax);
            
        // if ($query->num_rows() == 0 && $query1->num_rows() == 0) {
        if ($query->num_rows() == 0) {
            $data = array(
                'email' => $email,
                'password' => $password,
                'nama' => $name,
                'nik' => $nik,
                'nohp' => $nohp,
                'api_token' => $api_token,
                'remember_token' => $remember_token,
                // 'is_active' => 1,
                'is_active' => 0,
                'created_at' => date(DATE_ATOM, time())
            );     

            if ($this->db->insert($this->Users, $data)) {
                //on successfull insert operation

                $this->db->select_max('no_rm_pas');
                $this->db->from($this->Pasien);
                $getMax = $this->db->get();  
                
                foreach ($getMax->result() as $key) {
                    $angka = $key->no_rm_pas;
                }

                $bilangan = (int)$angka;

                // echo "$fzeropadded "; // Hasil 0001234

                $noReg = $bilangan + 1;
                $convertAngka = sprintf("%06d", $noReg);

                $dataPasien = array(
                        'no_rm_pas' => $convertAngka,
                        'nama_pas' => $name,
                        'ktp_pas' => $nik,
                        'telp_pas' => $nohp,
                        'tgl_daftar' => date(DATE_ATOM, time())
                    );

                $this->db->insert($this->Pasien, $dataPasien); //insert tb_pasien

                //Query to check if the email already exists in tb_Users
                $this->db->select('email');
                $this->db->from($this->Rsmm);
                $this->db->where('email', $email);
                $this->db->limit(1);
                $query1 = $this->db->get();

                if ($query1->num_rows() == 0) {
                    $data1 = array(
                        'email' => $email,
                        'username' => $email,
                        'password' => $password,
                        'nmlengkap' => $name,
                        'no_hp' => $nohp,
                        'status_aktif' => 'Y',
                        'sirsak_user' => '4',
                        'sirsak_level' => '1',
                        'login_at' => date(DATE_ATOM, time())
                    );

                    $this->db->insert($this->Rsmm, $data1); //insert tb_user

                    $data2 = array(
                        'no_rm_pas' => $email,
                        'nm_debitur' => $password,
                        'nomor_kartu' => $noBpjs,
                    ); 

                $this->db->insert($this->kartuBpjs, $data2);
                    return 1;
                }else{
                    //if user email already exists in tb_users
                    return 1;
                }

            } else {
                //if db insert operation fails
                return 0;
            }
        } else {
            //if user email already exists
            return 3;
        }
    }

    public function create_new_user($email, $nik, $encrypted_password, $nohp, $name, $api_token, $remember_token) {
        $password = $encrypted_password;

        //Query to check if the email already exists in tb_login 
        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('nik', $nik);
        $this->db->limit(1);
        $query = $this->db->get();     
        
        // if ($query->num_rows() == 0 && $query1->num_rows() == 0) {
        if ($query->num_rows() == 0) {
            $data = array(
                'email' => $email,
                'password' => $password,
                'nama' => $name,
                'nik' => $nik,
                'nohp' => $nohp,
                'api_token' => $api_token,
                'remember_token' => $remember_token,
                'is_active' => 0,
                // 'is_active' => 1,
                'created_at' => date(DATE_ATOM, time())
            );            

            if ($this->db->insert($this->Users, $data)) {
                //on successfull insert operation

                $this->db->select_max('no_rm_pas');
                $this->db->from($this->Pasien);
                $getMax = $this->db->get();  
                
                foreach ($getMax->result() as $key) {
                    $angka = $key->no_rm_pas;
                }

                $bilangan = (int)$angka;

                // echo "$fzeropadded "; // Hasil 0001234

                $noReg = $bilangan + 1;
                $convertAngka = sprintf("%06d", $noReg);

                $dataPasien = array(
                        'no_rm_pas' => $convertAngka,
                        'nama_pas' => $name,
                        'ktp_pas' => $nik,
                        'telp_pas' => $nohp,
                        'tgl_daftar' => date(DATE_ATOM, time())
                    );

                $this->db->insert($this->Pasien, $dataPasien); //insert tb_pasien

                //Query to check if the email already exists in tb_Users
                $this->db->select('email');
                $this->db->from($this->Rsmm);
                $this->db->where('email', $email);
                $this->db->limit(1);
                $query1 = $this->db->get();

                if ($query1->num_rows() == 0) {
                    $data1 = array(
                        'email' => $email,
                        'username' => $email,
                        'password' => $password,
                        'nmlengkap' => $name,
                        'no_hp' => $nohp,
                        'status_aktif' => 'Y',
                        'sirsak_user' => '4',
                        'sirsak_level' => '1',
                        'login_at' => date(DATE_ATOM, time())
                    );

                    $this->db->insert($this->Rsmm, $data1); //insert tb_user
                    return 1;
                }else{
                    //if user email already exists in tb_users
                    return 1;
                }

            } else {
                //if db insert operation fails
                return 0;
            }
        } else {
            //if user email already exists
            return 3;
        }
    }

    public function createpoli($poliklinik, $tgl, $caraked, $dokter, $debitur, $nokartu, $norujukan) {

            $data = array(
                'poliklinik' => $poliklinik,
                'tgl_reg_utama' => $tgl,
                // 'nama_penanggung_reg' => $name,
                'cara_datang' => $caraked,
                'debitur_reg_utama' => $debitur,
                'no_rm_pas_reg' => $nokartu,
                'created_at' => date(DATE_ATOM, time())
            );      
            
            // $this->db->insert($this->regisPoli, $data);
            if ($this->db->insert($this->regisPoli, $data)) {
                //on successfull insert operation
                return 1;
            } else {
                //if db insert operation fails
                return 0;
            }

           
    }

    public function getUserDetail($email)
    {
        $this->db->select('id_login, nohp');
        // $this->db->select('*');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getPasienRM($email)
    {
        $this->db->select('api_token, is_active');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function login($email, $encrypted_password) {
        //Query to fetch user token and match email & password
        $this->db->select('api_token, is_active, no_rm_pas, nama_pas, ktp_pas, nohp');
        // $this->db->select('*');
        $this->db->from($this->Users);
        $this->db->join('tb_pasien', 'tb_login.nik = tb_pasien.ktp_pas','left');
        $this->db->where('email', $email);
        $this->db->where('password', $encrypted_password);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function get_schjadwal_dokter($data)
    {
        $this->db->select('id_hari, nm_instalasi, id_dok, nmlengkap, kuota_pasien_online, jam_mulai, jam_selesai');
        $this->db->from('tb_praktekdokter');
        $this->db->join('tb_instalasi', 'tb_praktekdokter.id_sub = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'tb_praktekdokter.id_dok = tb_user.id_user','left');
        $this->db->order_by('id_hari', 'ASC');
        $this->db->order_by('nmlengkap', 'ASC');

        // $this->db->where('nmlengkap =', $data);
        // $this->db->like('hari', $data);
        // $this->db->like('nm_instalasi', $data);
        $this->db->like('nmlengkap', $data);
        // $this->db->limit(1);
        $query = $this->db->get();
        return $query->result(); 
    }

    public function get_sisa_quota($idDok)
    {
        $this->db->select('id_hari, nm_instalasi, id_dok, nmlengkap, kuota_pasien_online, jam_mulai, jam_selesai');
        $this->db->from('tb_register_meta');
        // $this->db->join('tb_instalasi', 'tb_praktekdokter.id_sub = tb_instalasi.id_ins','left');
        // $this->db->join('tb_user', 'tb_praktekdokter.id_dok = tb_user.id_user','left');
        $this->db->order_by('id_hari', 'ASC');
        $this->db->order_by('nmlengkap', 'ASC');

        // $this->db->where('nmlengkap =', $data);
        // $this->db->like('hari', $data);
        // $this->db->like('nm_instalasi', $data);
        $this->db->where('tgl_periksa_meta', $idDok);
        $this->db->where('tgl_periksa_meta', $idDok);
        $this->db->like('dokter_reg_meta', $idDok);
        // $this->db->limit(1);
        $query = $this->db->get();
        return $query->result(); 
        
    }   

    public function get_list_dokter()
    {
        $this->db->select('id_user, id_ins, nmlengkap');
        $this->db->from('tb_praktekdokter');
        $this->db->join('tb_instalasi', 'tb_praktekdokter.id_sub = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'tb_praktekdokter.id_dok = tb_user.id_user','left');
        $this->db->order_by('nmlengkap', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_list_poli()
    {
        $this->db->select('id_ins, nm_instalasi, id_dok, nmlengkap');
        $this->db->from('tb_praktekdokter');
        $this->db->join('tb_instalasi', 'tb_praktekdokter.id_sub = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'tb_praktekdokter.id_dok = tb_user.id_user','left');
        $this->db->group_by("nmlengkap"); 
        $this->db->order_by('nmlengkap', 'ASC');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_schjadwal_operasi($data)
    {
        // $this->db->select('*');
        $this->db->select('tgl_operasi, nmlengkap, nm_brg, nm_instalasi');
        $this->db->from('okvk_penjadwalanoperasi');
        $this->db->join('tb_instalasi', 'okvk_penjadwalanoperasi.id_sub_op = tb_instalasi.id_ins','left');
        // $this->db->join('tb_instalasi', 'okvk_penjadwalanoperasi.permintaan_dari = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'okvk_penjadwalanoperasi.permintaan_oleh = tb_user.id_user','left');
        // $this->db->join('tb_user', 'okvk_penjadwalanoperasi.operator_operasi = permintaan_oleh.id_user','left');
        $this->db->join('tb_tindakan', 'okvk_penjadwalanoperasi.id_operasi = tb_tindakan.id_brg','left');
        $this->db->join('tb_register', 'okvk_penjadwalanoperasi.id_reg = tb_register.id_reg','left');
        $this->db->join('tb_pasien', 'okvk_penjadwalanoperasi.no_rm_pas = tb_pasien.no_rm_pas','left');
        $this->db->like('tgl_operasi', date("Y-m-d"));
        $this->db->like('nmlengkap', $data);

        // $this->db->where('tgl_operasi', date("Y-m-d"));
        // $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();        
    }

    public function get_jadwal_dokter()
    {
        $this->db->select('id_hari, nm_instalasi, nmlengkap, kuota_pasien_online, jam_mulai, jam_selesai ');
        $this->db->from('tb_praktekdokter');
        $this->db->join('tb_instalasi', 'tb_praktekdokter.id_sub = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'tb_praktekdokter.id_dok = tb_user.id_user','left');
        $this->db->order_by('id_hari', 'ASC');
        $this->db->order_by('nmlengkap', 'ASC');

        // $this->db->where('is_deleted !=', '1');
        // $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();        
    }

    public function get_jadwal_operasi()
    {
        // $this->db->select('*');
        $this->db->select('tgl_operasi, nmlengkap, nm_brg, nm_instalasi');
        $this->db->from('okvk_penjadwalanoperasi');
        $this->db->join('tb_instalasi', 'okvk_penjadwalanoperasi.id_sub_op = tb_instalasi.id_ins','left');
        // $this->db->join('tb_instalasi', 'okvk_penjadwalanoperasi.permintaan_dari = tb_instalasi.id_ins','left');
        $this->db->join('tb_user', 'okvk_penjadwalanoperasi.permintaan_oleh = tb_user.id_user','left');
        // $this->db->join('tb_user', 'okvk_penjadwalanoperasi.operator_operasi = permintaan_oleh.id_user','left');
        $this->db->join('tb_tindakan', 'okvk_penjadwalanoperasi.id_operasi = tb_tindakan.id_brg','left');
        $this->db->join('tb_register', 'okvk_penjadwalanoperasi.id_reg = tb_register.id_reg','left');
        $this->db->join('tb_pasien', 'okvk_penjadwalanoperasi.no_rm_pas = tb_pasien.no_rm_pas','left');
        $this->db->like('tgl_operasi', date("Y-m-d"));
        // $this->db->where('tgl_operasi', date("Y-m-d"));
        // $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();        
    }

    function check_email_exists($email) {
        //Query to match email & verify if record is not deleted
        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('email', $email);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return false;
        }
    }

    /* For Change Password API */

    public function match_old_password($apiToken, $encryptedOldPassword) {
        //Query to fetch user password
        $this->db->select('password');
        $this->db->from($this->Users);
        $this->db->where('api_token', $apiToken);
        $this->db->where('password', $encryptedOldPassword);
        $this->db->where('is_deleted !=', '1');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {

            return true;
        } else {
            return false;
        }
    }

    public function modify_forgot_password($userToken, $encryptedNewPassword) {
        //Query to update the new password based on userToken
        $data = array(
            'password' => $encryptedNewPassword
        );
        $this->db->where('api_token', $userToken);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return 1;
        } else {
            //if db update operation fails
            return 0;
        }
    }

    /* For Change Password API ends */

    public function update_password_requested_time($email) {
        //Query to update password requested time
        $data = array(
            'password_requested_time' => date('Y-m-d H:i:s'),
            'is_password_requested' => 1
        );
        $this->db->where('email', $email);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return TRUE;
        } else {
            //if db update operation fails
            return FALSE;
        }
    }

    public function save_new_password($hash_email, $password_hash) {
        //Query to update the new password based on userToken
        $data = array(
            'password' => $password_hash,
            'is_password_requested' => 0
        );
        $this->db->where('remember_token', $hash_email);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return TRUE;
        } else {
            //if db update operation fails
            return FALSE;
        }
    }

    function get_password_requested_time($hash_email) {
        //Query to fetch upassword requested time based on user token
        $this->db->select('password_requested_time');
        $this->db->from($this->Users);
        $this->db->where('remember_token', $hash_email);
        $this->db->where('is_deleted !=', '1');
        $this->db->where('is_password_requested !=', '0');
        $this->db->limit(1);

        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return FALSE;
        }
    }


    public function activate_account($hash_email) {
        //Link expiration code	
        $this->load->helper('date');
        $date = date('Y-m-d H:i:s');
        $this->db->select('created_at');
        $this->db->from($this->Users);
        $this->db->where('remember_token', $hash_email);
        $query = $this->db->get();
        if (!empty($query)) {
            //echo $this->db->last_query();
            $row = $query->row();
            $date1Timestamp = strtotime(@$row->created_at);
            $date2Timestamp = strtotime($date);
            //Calculate the difference.
            $difference = $date2Timestamp - $date1Timestamp;
            if ($difference > 1800) {
                $this->db->delete($this->Users, array('remember_token' => $hash_email));
                return false;
            }
        }
        //link expiration code ends here
        //Query to update the new password based on userToken

        $data = array(
            'is_active' => "1"
        );

        $this->db->where('remember_token', $hash_email);

        if ($this->db->update($this->Users, $data)) {

            //on successfull update operation

            return TRUE;
        } else {

            //if db update operation fails

            return FALSE;
        }
    }

    public function insert_img($data_insert){
        $this->db->insert('tb_dok_kelengkapan',$data_insert);
    }


    public function modify_forgot_password_by_email($email, $encryptedNewPassword) {
        //Query to update the new password based on userToken
        $data = array(
            'password' => $encryptedNewPassword
        );
        $this->db->where('email', $email);
        if ($this->db->update($this->Users, $data)) {
            //on successfull update operation
            return 1;
        } else {
            //if db update operation fails
            return 0;
        }
    }

    public function getEmailByAuthToken($authToken) {

        $this->db->select('email');
        $this->db->from($this->Users);
        $this->db->where('api_token', $authToken);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->email;
        } else {
            return FALSE;
        }
    }

}