<?php

if(!defined('BASEPATH')) exit('No direct script access allowed!');

class SslModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function add_ssl($data) {
        try{
            return $this->db->insert('ssl_monitors',$data);
        }catch(Exception $e){
            return false;
        }
    }

    public function fetch_all_ssl_monitors($user_id,    $limit, $offset){
        try{
            return $this->db
                 ->where('user_id',$user_id)
                 ->limit($limit,$offset)
                 ->get('ssl_monitors')->result();
        }catch(Exception $e){
            return false;
        }
    }
    public function fetch_all_monitor_count($user_id){
        try{
             return $this->db->select('count(ssl_id) as totalMonitor')
                            ->where('user_id',$user_id)
                            ->get('ssl_monitors')->result()[0]->totalMonitor;

        }catch(Exception $e){
            return 0;
        }
    }


    public function delete_ssl_monitor($ssl_id){
        try{
             return $this->db->where(['ssl_id'=>$ssl_id])
                            ->delete('ssl_monitors');

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