<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Cron extends CI_Controller {

	/**
	 * This controller includes all the cron functionalities
     * 
     *  To run this controller use command below -
     *  $ php [directory]/index.php controller_name method_name arguments
	 * 
	 */
	public function __construct(){

		parent::__construct();
		$this->load->model('CronModel');
		$this->load->model('MailModel');
        $this->load->helper('MY_cron');
	}
	public function index(){	

	}


    public function check(){
		echo "<pre>";

        if($monitors = $this->CronModel->get_all_monitors()){

			$urls=[];
			//print_r($monitors);
            foreach ($monitors as $monitor){

                array_push($urls,	$monitor->protocol."://".$monitor->url);
				
            }
			if($results = parallelCurl($urls,500)){
				//print_r($results);


				$checks_data = [];
				$last_check_times = [];
				$failed_monitor_ids = [];
				$failed_monitor_details = []; //for inserting incident details into database 
				foreach ($results as $key => $result){
					//print_r($result);
					$http_code = $result["info"]["http_code"];
					if($http_code != 200){
						array_push($failed_monitor_ids,$monitors[$key]->monitor_id); // store failed monitor ids

						// ready incident data where array key is monitor id
						$data =	[
							'monitor_id' 	=>	$monitors[$key]->monitor_id,
							'response_code'	=>	$http_code,
							'incident_time' => date("Y-m-d H:i:s")
						];
						array_push($failed_monitor_details,$data);
					}

					$data=[
						'monitor_id' 	=>	$monitors[$key]->monitor_id,
						'status'		=>	$http_code===200?true:false,
						'response_time'	=>	$result["info"]["connect_time"] * 1000, //to make response time in ms
						'response_code'	=>	$http_code
					];
					
					array_push($checks_data,$data);
					array_push($last_check_times,[
						"status"	 =>	($http_code == 200 ? true :false),
						"monitor_id" => $data["monitor_id"],
						"checked_at" => date("Y-m-d H:i:s")
						]
					);
				}

				// find the monitor ids who were online in the last check
				$down_monitor_ids = $this->CronModel->last_online_moniotors_where_monitor_id_in($failed_monitor_ids);

				
				$recent_down_monitor_ids = [];
				foreach($down_monitor_ids as $down_monitor_id){
					array_push($recent_down_monitor_ids,$down_monitor_id->monitor_id);
				}

				// removing monitor data which were offline previously also,
				// we need only the monitor data which were previously online but now offline

				foreach($failed_monitor_details as $key=>$data){

					// remove array items(down monitor data) which are not in $recent_down_monitor_ids
					if(array_search($data['monitor_id'],$recent_down_monitor_ids) === false){
							unset($failed_monitor_details[$key]);
						}
				}
				// reindex array after removing the monitor data which were offline before also
				$failed_monitor_details = array_values($failed_monitor_details);

				// insert incident data
				if(count($failed_monitor_details) > 0){
					$this->CronModel->insert_incidents($failed_monitor_details);
				}

				//$last_online_monitor_where_monitor_id_in($)
				echo "<pre>";
				
;
				
				if(!$this->CronModel->insert_multi_checks($checks_data,	$last_check_times)){
					file_put_contents(LOG_DIRECTORY,'checks failed to insert',FILE_APPEND);
				}

				/**
				 * 
				 * insert incident times
				 */
				if($recent_down_monitor_ids){

				}
				
				/**
				 * if falied monitor are previously alerted or not
				 */
				if($recent_down_monitor_ids){
					$emails = $this->CronModel->get_email_id_by_monitor_id($recent_down_monitor_ids);
					$email_list = [];
					foreach($emails as $email){
						array_push($email_list,$email->email);
					}
					print_r($email_list);
					
					// send mail
					$this->MailModel->sendmail_bulk($email_list);
				}
				
				
				//
				/*
				echo "alaert sent for";
				print_r($recent_down_monitor_ids);
				echo "<b> incidinet monitor data<br>";
				print_r($failed_monitor_details);
				*/
				
			}
        }
    }

	public function domain_check(){
		$domain_details = $this->CronModel->fetch_domain_email_ids();

		$subject ='Domain Expiring Soon';
		$content='';
		foreach($domain_details as $domain){
			$content='Your domain '.$domain->url.' is expiring in '.$domain->remind_before_days.' days. Renew your domain as soon as possible';
			$this->MailModel->send_mail_by_email($domain->email, $subject, $content);
		}
	}


	public function ssl_check(){
		$domain_details = $this->CronModel->fetch_ssl_email_ids();

		$subject ='SSL Certificate Expiring Soon';
		$content='';
		foreach($domain_details as $domain){
			$content='Your SSL certificate for domain '.$domain->url.' is expiring in '.$domain->remind_before_days.' days. Renew your SSL certificate as soon as possible';
			$this->MailModel->send_mail_by_email($domain->email, $subject, $content);
		}
	}

	public function recheck_ssl_details(){
		$ssl_datas = $this->CronModel->fetch_null_ssl_ids();
		foreach($ssl_datas as $ssl_data){
			$new_data = fetch_ssl_details($ssl_data->protocol.'://'.$ssl_data->url);
			//print_r($new_data);
			if($new_data && $new_data["issuer"]){
				$this->CronModel->update_ssl_data($ssl_data->ssl_id, $new_data);
			}
		}
	}

	public function recheck_domain_details(){
		$domain_datas = $this->CronModel->fetch_null_domain_ids();
		foreach($domain_datas as $domain_data){
			$new_data = get_domain_details($domain_data->protocol.'://'.$domain_data->url);
			//echo "<pre>";
			//echo $domain_data->protocol.'://'.$domain_data->url;
			//print_r($new_data);
			if($new_data && $new_data["expire_on"]){
				$this->CronModel->update_domain_data($domain_data->domain_id, $new_data);
			}
		}
	}

}
