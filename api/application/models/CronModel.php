<?php

if(!defined('BASEPATH')) exit('No direct script access allowed!');

class CronModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }
/*
    public function add_monitor($data) {
        try{
            return $this->db->insert('monitors',$data);
        }catch(Exception $e){
            return false;
        }
    }

    public function getUserDetails($email) {

        $this->db->where('email',$email);
        $q = $this->db->get('users');

        if ($q->num_rows() > 0){
            $data = $q->result()[0];
            $q->free_result();
            return $data;
        }
        return false;
    }
*/

    public function get_all_monitors(){
        try{
            return $this->db->query('SELECT protocol,url,monitors.monitor_id FROM monitors,last_checks WHERE monitors.monitor_id=last_checks.monitor_id and now() > TIMESTAMPADD(second,(check_interval-30),checked_at)')
                            ->result();
        }catch(Exception $e){
            file_put_contents(LOG_DIRECTORY,$e->get_message(),FILE_APPEND);
            return false;
        }
    }

    public function get_email_id_by_monitor_id($failedMonitors){
        try{
            return $this->db->select('distinct(email)')
                        ->from('monitors')
                        ->join('users','monitors.user_id=users.user_id')
                        ->where_in('monitor_id',$failedMonitors)
                        ->get()
                        ->result();
        }catch(Exception $e){
            file_put_contents(LOG_DIRECTORY,$e->get_message(),FILE_APPEND);
            return [];
        }
    }


    public function last_online_moniotors_where_monitor_id_in($failedMonitor){
        try{
             return $this->db->select('monitor_id')      
                        ->where_in('monitor_id',$failedMonitor)
                        ->where('status', true)
                        ->get('last_checks')
                        ->result(); 
        }catch(Exception $e){
            file_put_contents(LOG_DIRECTORY,$e->get_message(),FILE_APPEND);
            return [];
        }
    }

    public function insert_multi_checks($data,  $last_checked_data){
        try{
            $this->db->trans_start();
            $this->db->insert_batch('checks',$data); //insert checks details
            $this->db->update_batch('last_checks',$last_checked_data,'monitor_id'); //update last check time
            $this->db->trans_complete();

            return true;

        }catch(Exception $e){
            return false;
        }
    }

    public function insert_incidents($failed_monitor_details){
        return $this->db->insert_batch('incidents',$failed_monitor_details);
    }












    /*                       Domain Check                         */

    public function fetch_domain_email_ids(){

        try{
            return $this->db->query('SELECT name,email,url,remind_before_days,expire_on FROM users,domains WHERE users.user_id=domains.user_id AND remind_before_days-1 = TIMESTAMPDIFF(DAY,now(),expire_on)')
                     ->result();
        }catch(Exception $e){
            return [];
        }
    }

    public function fetch_ssl_email_ids(){

        try{
            return $this->db->query('SELECT name,email,url,remind_before_days,end_date FROM users,ssl_monitors WHERE users.user_id=ssl_monitors.user_id AND remind_before_days-1 = TIMESTAMPDIFF(DAY,now(),end_date)')
                     ->result();
        }catch(Exception $e){
            return [];
        }
    }

    public function fetch_null_ssl_ids(){
        try{
            return $this->db->select('ssl_id,protocol,url')
                     ->where('end_date',NULL)
                     ->get('ssl_monitors')->result();
        }catch(Exception $e){
            return [];
        }
    }

    public function update_ssl_data($ssl_id,$data){
        try{
            $this->db->where('ssl_id', $ssl_id)
                    ->update('ssl_monitors',$data);
        }catch(Exception $e){
            echo "Something went wrong";
        }
    }

    public function fetch_null_domain_ids(){
        try{
            return $this->db->select('domain_id,protocol,url')
                     ->where('expire_on',NULL)
                     ->get('domains')->result();
        }catch(Exception $e){
            return [];
        }
    }

    public function update_domain_data($domain_id,$data){
        try{
            $this->db->where('domain_id', $domain_id)
                    ->update('domains',$data);
        }catch(Exception $e){
            echo "Something went wrong";
        }
    }
}


