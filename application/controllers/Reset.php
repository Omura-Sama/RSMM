<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');


class Reset extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	// public function index()
	// {
	// 	$this->load->view('welcome_message');
	// }
	//  function __construct($config = 'rest') {
    //     parent::__construct($config);
    //     $this->load->database();
    // }

	public function index($nik,$email)
	{

        // $nik = $this->get('nik');
        // $email = $this->get('email');
        // $dataKode = $this->api_model->getKodeverifikasi($kode);
        $data['nik'] = $nik;
		$data['email'] =  $email;
		// $data['msg'] =  $;

		$this->load->view('reset_password',$data);
		// $id= $this->get('no_rm_pas');
		// if ($id == '') {
		// 	$pasien = $this->db->get('tb_pasien')->result();
		// }else{
		// 	$this->db->where('no_rm_pas', $id);
		// 	$pasien = $this->db->get('tb_pasien')->result();
		// }
		// $this->response($pasien, 200);
	}

    public function resetPassword()
    {
        $nik = $this->input->post('nik');
        $email = $this->input->post('email');
        $pas1 = $this->input->post('newpass');
        $pas2 = $this->input->post('confirmpass');

        // echo $nik;
        // echo $email;
        // echo $pas1;
        // echo $pas2;

        if ($pas1 == $pas2) {
            $encrypted_password = hash('sha512', $pas1);

            $editUser = array(
                'password' => $encrypted_password
            );
            $this->db->where('nik', $nik);
            $this->db->where('email', $email);
            $data = $this->db->update('tb_login', $editUser); 
            if (!empty($data)) {
                $message = "Password Berhasil Di Ubah";
                echo "<script type='text/javascript'>alert('$message'); myWindow.close();</script>";
            }else{
                $message = "Password Gagal Di Ubah";
                echo "<script type='text/javascript'>alert('$message'); window.history.go(-1);</script>";
            }

        }else {
            $message = "Password Tidak Sama";
            echo "<script type='text/javascript'>alert('$message'); window.history.go(-1);</script>";
        }

    }
}
