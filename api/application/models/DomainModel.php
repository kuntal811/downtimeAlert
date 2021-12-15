<?php

if(!defined('BASEPATH')) exit('No direct script access allowed!');

class DomainModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function add_domain($data) {
        try{
            return $this->db->insert('domains',$data);
        }catch(Exception $e){
            return false;
        }
    }

    public function fetch_all_domain_monitors($user_id,    $limit, $offset){
        try{
            return $this->db
                 ->where('user_id',$user_id)
                 ->limit($limit,$offset)
                 ->get('domains')->result();
        }catch(Exception $e){
            return false;
        }
    }
    public function fetch_all_monitor_count($user_id){
        try{
             return $this->db->select('count(domain_id) as totalMonitor')
                            ->where('user_id',$user_id)
                            ->get('domains')->result()[0]->totalMonitor;

        }catch(Exception $e){
            return 0;
        }
    }


    public function delete_domain_monitor($domain_id){
        try{
             return $this->db->where(['domain_id'=>$domain_id])
                            ->delete('domains');

        }catch(Exception $e){
            return false;
        }
    }
    public function update_monitor($monitor_id, $data) {
        try{

            return $this->db->where('monitor_id',$monitor_id)
                            ->update('monitors', $data);

        }catch(Exception $e){
            return false;
        }
    }

}
?>