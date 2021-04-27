<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class User extends REST_Controller {

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
		$id= $this->get('id');
		if ($id == '') {
			$user = $this->db->get('tb_pasien')->result();
		}else{
			$this->db->where('id', $id);
			$user = $this->db->get('tb_pasien')->result();
		}
		$this->response($user, 200);
	}

	public function register_post() {
    // public function index_get() {
        $email = $this->post('email');
        $password = $this->post('password');
        $name = $this->post('name');

        $encrypted_password = hash('sha512', $password);
        $api_token = hash('sha512', $email);

        //For change password web interface as unique key
        $remember_token = md5($email);

        $register_user = $this->api_model->create_new_user($email, $encrypted_password, $name, $api_token, $remember_token);

        if ($register_user == 1) {
            //Encode user email to attach to the url 
            $url = base_url() . "welcome/verify_email/" . $remember_token;

            //Send email with credentials to user
            $this->email->from('info@domain.com', 'App Name');
            $this->email->to($email);
            $this->email->subject('Account Verification');
            $this->email->message('Dear Customer,
							 <br/><br/>
							 Please click on the following LINK to verify your account:<br/>
							 <a href="' . $url . '">Verify</a><br/>
                                                         <br/><br/>
							 Best Regards,<br/>');
            $this->email->send();
            $message = array(
                'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "User Registered Successfully."),
                'body' => array("apiToken" => $api_token)
            );
        } else if ($register_user == 3) {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0004", "resultMessage" => "Email already exists"),
                'body' => array()
            );
        } else {
            $message = array(
                'header' => array("result" => "false", "resultCode" => "0404", "resultMessage" => "Unable to register a user account"),
                'body' => array()
            );
        }

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
    }

    public function login_post() {

        $email = $this->post('email');
        $password = $this->post('password');

        $encrypted_password = hash('sha512', $password);
        $login_user = $this->api_model->login($email, $encrypted_password);
        if ($login_user != FALSE) {
            $is_active = $login_user[0]->is_active;
            if ($is_active != 0) {
                $api_token = $login_user[0]->api_token;
                $message = array(
                    'header' => array("result" => "true", "resultCode" => "0000", "resultMessage" => "User logged in Successfully"),
                    'body' => array("apiToken" => $api_token)
                );
            } else {
                $message = array(
                    'header' => array("result" => "false", "resultCode" => "0005", "resultMessage" => "You need to verify your email in order to login."),
                    'body' => array()
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
}
