<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SignUp extends CI_Controller {

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

        if(!isset($data->name) || !isset($data->email) || !isset($data->password)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }

        // Clean user input 
        $name = clean_input_data($data->name);
        $email = clean_input_data($data->email);
        $password = clean_input_data($data->password);

        

        if( validate_input($name,'name') && validate_input($email,'email') && validate_input($password,'password') ){

            // If form data is valid
            $regData = [

                'name'      => $name,
                'email'     => $email,
                'password'  => password_hash($password, PASSWORD_BCRYPT),
            ];
    
            if($this->UserModel->create($regData)){
                $response = [
                    'status'    => true,
                    'message'   => 'Registration successfully !',
                ];
                http_response_code(200);

            }else{
                $response = [
                    'status'    => false,
                    'message'   => "Something wents wrong ! Please try again"
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
                'message' => "Please provide valid form details.",
            ];
            http_response_code(200);
            
        }
        
        echo json_encode($response);
	}

}
