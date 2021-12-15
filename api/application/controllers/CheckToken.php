<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CheckToken extends CI_Controller {

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
	}
	public function index(){	
		header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Origin, Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
            $auth = $_SERVER["HTTP_AUTHORIZATION"];
            $auth_array = explode(" ", $auth);
            try{
                $decoded = JWT::decode( $auth_array[1], new Key(SECRET_KEY, 'HS256') );
                $decoded = (array)$decoded;


                $response = [
                    'status' => true,
                    'message' => "Valid token",
                ];
                http_response_code(200);

            }catch(Exception $e){
                //print_r($e->getMessage());

                $response = [
                    'status' => false,
                    'message' => "Invalid token",
                ];
                http_response_code(200);
            }
            }else{
                $response = [
                    'status' => false,
                    'message' => "Token not found",
                ];
                http_response_code(200);
            }
            
        echo json_encode($response);
    }

}
