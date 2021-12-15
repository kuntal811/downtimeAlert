<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Monitor extends CI_Controller {

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
		$this->load->model('MonitorModel');
        $this->load->helper('MY_formValidation');
        $this->load->helper('MY_tokenValidation');
	}
	public function index(){	
        /*
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
					);

					// generate token
					$JWT = JWT::encode($payload, SECRET_KEY, 'HS256');

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

            $response = [
                'status' => false,
                'message' => "Please provide correct formated field value.",
            ];
            http_response_code(200);
            
        }

		echo json_encode($response);
        */
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
		if(!isset($data->title) || !isset($data->url) || !isset($data->interval)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }
		
		 // Clean user input 
		 $title = clean_input_data($data->title);
		 $url = clean_input_data($data->url);
         $interval = clean_input_data($data->interval);

         // parse url
         $url = parse_url($url);
         // prepare data according to database
         $data=[
             'user_id'          => $userData["user"]->userId,
             'title'            => $title,
             'check_interval'   => $interval * 60,
             'protocol'         => $url["scheme"],
             'url'              => $url["host"],
         ];

         if($monitor_id = $this->MonitorModel->add_monitor($data)){

            $response = [
                'status'    => true,
                'message'   => "Monitor added successfully"
            ];
            http_response_code(200);

         }else{
             // failed to insert
            $response = [
                'status'    => false,
                'message'   => "Failed to add monitor",
            ];
            http_response_code(200);
         }

        echo json_encode($response);
    }

    public function update(){
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
		if(!isset($data->title) || !isset($data->monitor_id) || !isset($data->interval)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }
		
		 // Clean user input 
		 $title = clean_input_data($data->title);
		 $monitor_id = clean_input_data($data->monitor_id);
         $interval = clean_input_data($data->interval);

         // parse url
         //$url = parse_url($url);
         // prepare data according to database
         $data=[
             'user_id'          => $userData["user"]->userId,
             'title'            => $title,
             'check_interval'   => $interval,
         ];

         if($monitor_id = $this->MonitorModel->update_monitor($monitor_id, $data)){

            $response = [
                'status'    => true,
                'message'   => "Monitor updated successfully"
            ];
            http_response_code(200);

         }else{
             // failed to insert
            $response = [
                'status'    => false,
                'message'   => "Failed to updated monitor",
            ];
            http_response_code(200);
         }

        echo json_encode($response);
    }



    /**
     * function fetch_all_monitor
     * 
     * @method GET
     * @return array containing all monitor details with last check details
     * 
     */


    public function fetch_all_monitors(){
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


        if($monitors = $this->MonitorModel->fetch_all_monitors($userData["user"]->userId,   $limit, $offset)){

            $monitors_data=[];

            foreach ($monitors as $monitor){

                $response_and_status = $this->MonitorModel->fetch_monitor_status($monitor->monitor_id);
                

                $monitor_data=[
                    'id'            =>  $monitor->monitor_id,
                    'title'         =>  $monitor->title,
                    'last_checked'  =>  $this->MonitorModel->fetch_last_checked_time($monitor->monitor_id),
                    'status'        =>  $response_and_status?$response_and_status->status:NULL,
                    'response_time' =>  $response_and_status?$response_and_status->response_time:NULL,
                    'uptime'        =>  $this->MonitorModel->fetch_monitor_avg_uptime($monitor->monitor_id),
                    'last_incident' =>  $this->MonitorModel->fetch_monitor_last_incident($monitor->monitor_id),
                ];

                array_push($monitors_data,  $monitor_data);
            }
            $total = $this->MonitorModel->fetch_all_monitor_count($userData["user"]->userId);
            $up     = $this->MonitorModel->fetch_up_monitor_count($userData["user"]->userId);
            $response = [
                'monitors_data' => $monitors_data,
                'total_monitors' => $total,
                'up_monitors'   => $up,
                'down_monitors' => $total-$up
            ];
            echo json_encode($response);
        }
    }


    public function monitor_details(){
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
		if(!isset($data->monitor_id)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }

        $monitor_id = $data->monitor_id;
        //print_r(intval($monitor_id));

        $monitor_id = intval($monitor_id);
        $monitor_id = $monitor_id !=0 ? $monitor_id :  false;

        if(!$monitor_id){
            $response = [
                'status'    => false,
                'message'   => "Field required",
            ];

            http_response_code(200);
            echo json_encode($response);
            return;
        }

        if($monitor_details = $this->MonitorModel->fetch_single_monitor_details($monitor_id)){


            $status = $this->MonitorModel->fetch_monitor_status($monitor_id);
            
            $monitor_data=[
                    'id'                =>  $monitor_details->monitor_id,
                    'title'             =>  $monitor_details->title,
                    'protocol'          =>  $monitor_details->protocol,
                    'url'               =>  $monitor_details->url,
                    'status'            =>  $status ? $status->status : NULL,
                    'interval'          =>  $monitor_details->check_interval,
                    'is_active'         =>  $monitor_details->is_active,
                    'created_at'        =>  $monitor_details->created_at,
                    'avg_response_time' =>  intval($this->MonitorModel->fetch_monitor_avg_response_time($monitor_id)),
                    'last_checked'      =>  $this->MonitorModel->fetch_last_checked_time($monitor_id),
                    'uptime'            =>  $this->MonitorModel->fetch_monitor_avg_uptime($monitor_id),
                    'incident_count'    =>  $this->MonitorModel->fetch_monitor_incident_count($monitor_id),
                    'monitor_checks'    => $this->MonitorModel->fetch_monitor_checks($monitor_id),
                    'monitor_graph'     => $this->MonitorModel->fetch_monitor_graph_data($monitor_id),
                ];

            echo json_encode($monitor_data);
        }
    }



    public function delete_monitor(){
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
		if(!isset($data->monitor_id)){
            $response = [
                'status'    => false,
                'message'   => "Please provide all the required field.",
            ];
            http_response_code(200);
            echo json_encode($response);
            return;
        }

        $monitor_id = $data->monitor_id;
        //print_r(intval($monitor_id));

        $monitor_id = intval($monitor_id);
        $monitor_id = $monitor_id !=0 ? $monitor_id :  false;

        if(!$monitor_id){
            $response = [
                'status'    => false,
                'message'   => "Field required",
            ];

            http_response_code(200);
            echo json_encode($response);
            return;
        }

        if($this->MonitorModel->delete_monitor($monitor_id)){


            $status = $this->MonitorModel->fetch_monitor_status($monitor_id);
            $response=[
                    'status'    =>  true,
                    'message'   => 'Monitor deleted successfully'
                ];

        }else{

            $response=[
                    'status'    =>  false,
                    'message'   => 'Failed to delete monitor'
                ];
        }

        echo json_encode($response);
    }

}
