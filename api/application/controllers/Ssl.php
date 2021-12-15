<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Ssl extends CI_Controller {

	public function __construct(){

		parent::__construct();
		$this->load->model('SslModel');
        $this->load->helper('MY_cron');
        $this->load->helper('MY_formValidation');
        $this->load->helper('MY_tokenValidation');
	}


    public function fetch_all_ssl_monitors(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Origin, Access-Control-Allow-Origin,Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


        /**
         * token validation function
         * @return array payload if valid token otherwise @return false
         * 
         */

        if(!$userData = validateToken()){
            $response = [
                'status'    => false,
                'message'   => "Unauthorized access",
            ];
            http_response_code(401);
            echo json_encode($response);
            return;
        }
        //print_r($userData);

        // retrieving page no and limit

        $page_no = intval($this->input->get('pageno'));
        //$page_no = $page_no !=0 ? $page_no :  1;

        $limit = intval($this->input->get('limit'));
        $limit = $limit !=0 ? $limit :  6;   // if limit is not givent then default is 6

        // calculate offset
        $offset = ( $page_no ) * $limit;


        if($monitors = $this->SslModel->fetch_all_ssl_monitors($userData["user"]->userId,   $limit, $offset)){

            $response = [
                'ssl_monitors' => $monitors,
                'total_monitors' => $this->SslModel->fetch_all_monitor_count($userData["user"]->userId)
            ];
            
            echo json_encode($response);
        }
    }

    public function delete_ssl_monitor(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Origin, Access-Control-Allow-Origin,Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


        /**
         * token validation function
         * @return array payload if valid token otherwise @return false
         * 
         */

        if(!$userData = validateToken()){
            $response = [
                'status'    => false,
                'message'   => "Unauthorized access",
            ];
            http_response_code(401);
            echo json_encode($response);
            return;
        }

        $data = json_decode( file_get_contents('php://input'));
		
		// check if required filed has passed
		if(!isset($data->ssl_id)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }

        $ssl_id = $data->ssl_id;
        //print_r(intval($monitor_id));

        $ssl_id = intval($ssl_id);
        $ssl_id = $ssl_id !=0 ? $ssl_id :  false;

        if(!$ssl_id){
            $response = [
                'status'    => false,
                'message'   => "Field required",
            ];

            http_response_code(200);
            echo json_encode($response);
            return;
        }

        if($this->SslModel->delete_ssl_monitor($ssl_id)){

            $response=[
                    'status'    =>  true,
                    'message'   => 'SSL monitor deleted successfully'
                ];

        }else{

            $response=[
                    'status'    =>  false,
                    'message'   => 'Failed to delete SSL monitor'
                ];
        }

        echo json_encode($response);
    }


    public function add(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Origin, Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


        /**
         * token validation function
         * @return array payload if valid token otherwise @return false
         * 
         */

        if(!$userData = validateToken()){
            $response = [
                'status'    => false,
                'message'   => "Unauthorized access",
            ];
            http_response_code(401);
            echo json_encode($response);
            return;
        }



		$data = json_decode( file_get_contents('php://input'));

        // check if required filed has passed
		if(!isset($data->url)   ||  !isset($data->remind_before_days)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }
		
		 // Clean user input 
		 $remind_before_days = clean_input_data($data->remind_before_days);
		 $url = clean_input_data($data->url);
         //$interval = clean_input_data($data->interval);

         // parse url
         $url = parse_url($url);
         // prepare data according to database
         $ssl_data = fetch_ssl_details($url["scheme"].'://'.$url["host"]);
         //var_dump($ssl_data);
         //exit();

         if($ssl_data){
            $data=[
                'user_id'          => $userData["user"]->userId,
                'protocol'         => $url["scheme"],
                'url'              => $url["host"],
                'remind_before_days'    => intval($remind_before_days)
            ]   +   $ssl_data;
         }else{
            $data=[
                'user_id'          => $userData["user"]->userId,
                'protocol'         => $url["scheme"],
                'url'              => $url["host"],
                'remind_before_days'    => intval($remind_before_days)
            ];
        }
         if($monitor_id = $this->SslModel->add_ssl($data)){

            $response = [
                'status'    => true,
                'message'   => "SSL monitor added successfully"
            ];
            http_response_code(200);

         }else{
             // failed to insert
            $response = [
                'status'    => false,
                'message'   => "Failed to add SSL monitor",
            ];
            http_response_code(200);
         }

        echo json_encode($response);


    }


}


