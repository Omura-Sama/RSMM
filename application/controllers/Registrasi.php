<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Registrasi extends REST_Controller {

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
	 function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->database();
    }

	public function index_get()
	{
		$email= $this->get('email');
		$pass= $this->get('password');
		if ($email == '') {
			$login = $this->db->get('tb_login')->result();

			if ($login != null) {
				$this->response(
					array(
					'error' => 'false',
					'message' => 'Login Successful',
					'data' => $login)
				);
			}else{
				$this->response(
					array(
					'error' => 'true',
					'message' => 'Login Fail',
					'data' => $login)
				);
			}
		}else{
			$this->db->where('email', $email);
			$this->db->where('password', $pass);
			$login = $this->db->get('tb_login')->result();

			if ($login != null) {
				$this->response(
					array(
					'error' => 'false',
					'message' => 'Login Successful',
					'data' => $login)
				);
			}else{
				$this->response(
					array(
					'error' => 'true',
					'message' => 'Login Fail',
					'data' => $login)
				);
			}
		}


	}
	
    //Mengirim atau menambah data kontak baru
    function index_post() {
        $data = array(
                    'nik'		=> $this->post('nik'),
                    'nama'      => $this->post('nama'),
                    'nohp'    	=> $this->post('nohp'),
                    'email'    	=> $this->post('email'),
					'password'  => $this->post('password'));
		
		$insert = $this->db->insert('tb_login', $data);
		
        if ($insert) {
            $this->response($data, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }
}
