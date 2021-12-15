<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Domain extends CI_Controller {

	public function __construct(){

		parent::__construct();
		$this->load->model('DomainModel');
        $this->load->helper('MY_formValidation');
        $this->load->helper('MY_tokenValidation');
        $this->load->helper('MY_cron');
	}


    public function fetch_all_domain_monitors(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST,GET");
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


        if($monitors = $this->DomainModel->fetch_all_domain_monitors($userData["user"]->userId,   $limit, $offset)){

            $response = [
                'domain_monitors' => $monitors,
                'total_monitors' => $this->DomainModel->fetch_all_monitor_count($userData["user"]->userId)
            ];
            
            echo json_encode($response);
        }
    }

    public function delete_domain_monitor(){
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
		if(!isset($data->domain_id)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }

        $domain_id = $data->domain_id;
        //print_r(intval($monitor_id));

        $domain_id = intval($domain_id);
        $domain_id = $domain_id !=0 ? $domain_id :  false;

        if(!$domain_id){
            $response = [
                'status'    => false,
                'message'   => "Field required",
            ];

            http_response_code(200);
            echo json_encode($response);
            return;
        }

        if($this->DomainModel->delete_domain_monitor($domain_id)){

            $response=[
                    'status'    =>  true,
                    'message'   => 'Domain monitor deleted successfully'
                ];

        }else{

            $response=[
                    'status'    =>  false,
                    'message'   => 'Failed to delete Domain monitor'
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
		if(!isset($data->url)||  !isset($data->remind_before_days)){
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
         
         $domain_details = get_domain_details($url["host"]);

         // prepare data according to database
         if($domain_details){
            $data=[
                'user_id'          => $userData["user"]->userId,
                'protocol'         => $url["scheme"],
                'url'              => $url["host"],
                'remind_before_days'    => intval($remind_before_days)
            ] + $domain_details;
         }else{
            $data=[
                'user_id'          => $userData["user"]->userId,
                'protocol'         => $url["scheme"],
                'url'              => $url["host"],
                'remind_before_days'    => intval($remind_before_days)
            ];
         }


         if($monitor_id = $this->DomainModel->add_domain($data)){

            $response = [
                'status'    => true,
                'message'   => "Domain added successfully"
            ];
            http_response_code(200);

         }else{
             // failed to insert
            $response = [
                'status'    => false,
                'message'   => "Failed to add domain",
            ];
            http_response_code(200);
         }

        echo json_encode($response);
    }

}


