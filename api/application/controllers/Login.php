<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Login extends CI_Controller {

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
	 * 
	 */
	public function __construct(){

		parent::__construct();
		$this->load->model('UserModel');
        $this->load->helper('MY_formValidation');
	}
	public function index(){	
		header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Origin, Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

		$data = json_decode( file_get_contents('php://input'));
		
		// check if required filed has passed
		if(!isset($data->email) || !isset($data->password)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }
		
		 // Clean user input 
		 $email = clean_input_data($data->email);
		 $password = clean_input_data($data->password);
		 
		 if(validate_input($email,'email') && validate_input($password,'password') ){
			
            if($userData = $this->UserModel->getUserDetails($email)){

				if(	password_verify($password,$userData->password)	){

					// if password correct

					// create payload
					$payload = array(
						"iss" => base_url(),
						"aud" => base_url(),
						"iat" => time(),
						"nbf" => time()+10,
						"user"=> [
							'name'		=>	$userData->name,
							'email'		=>	$userData->email,
							'userId'	=>	$userData->user_id
						]
					);

					// generate token
					$JWT = JWT::encode($payload, SECRET_KEY, 'HS256');

					//setcookie('dt_auth', $JWT, time() + (86400 * 30), "/", 'http://localhost:3000', false ,true); // 86400 = 1 day
					/*
					set_cookie([
						'name'	=> 'auth',
						'value'	=> 'testttt',
						'expire'=> time() + (86400 * 30),
						'httponly'=>true
					]);
					*/
					$response = [
						'status'    => true,
						'message'   => 'Login successfully !',
						'token'		=> $JWT
					];
					http_response_code(200);
				}else{

					//if incorrect password
					$response = [
						'status'    => false,
						'message'   => 'Wrong password',
					];
					http_response_code(200);
				}


            }else{
                $response = [
                    'status'    => false,
                    'message'   => "Invalid Username"
                ];
    
                http_response_code(200);
            }
        }else{
            //  if form has an error

            /*
            // Get error messages
            $error = [
                'name' => validate_input($name,'name'),
                'email' => validate_input($email,'email'),
                'password' => validate_input($password,'password')
            ];
            */

            $response = [
                'status' => false,
                'message' => "Please provide correct formated field value.",
            ];
            http_response_code(200);
            
        }

		echo json_encode($response);
	}

}
