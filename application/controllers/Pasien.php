<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Pasien extends REST_Controller {

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
		$id= $this->get('no_rm_pas');
		if ($id == '') {
			$pasien = $this->db->get('tb_pasien')->result();
		}else{
			$this->db->where('no_rm_pas', $id);
			$pasien = $this->db->get('tb_pasien')->result();
		}
		$this->response($pasien, 200);
	}
}
