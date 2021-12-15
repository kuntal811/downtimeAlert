<?php

if(!defined('BASEPATH')) exit('No direct script access allowed!');

class MonitorModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function add_monitor($data) {
        try{
            $this->db->trans_start();
            $this->db->insert('monitors',$data);

            $insert_id = $this->db->insert_id();

            $this->db->insert('last_checks',['monitor_id'=>$insert_id]);
            $this->db->trans_complete();
            return $insert_id;

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


    public function fetch_all_monitors($user_id,    $limit, $offset){
        try{
            return $this->db->select('monitor_id, title')
                 ->where('user_id',$user_id)
                 ->limit($limit,$offset)
                 ->get('monitors')->result();
        }catch(Exception $e){
            return false;
        }
    }

    public function fetch_last_checked_time($monitor_id){
        try{
            return $this->db->select('checked_at')
                            ->where('monitor_id',$monitor_id)
                            ->get('last_checks')->result()[0]->checked_at;
        }catch(Exception $e){
            return NULL;
        }
    }

    public function fetch_monitor_status($monitor_id){
        try{
             $q = $this->db->select('status,response_time')
                            ->where('monitor_id',$monitor_id)
                            ->order_by('checked_at','DESC')
                            ->limit(1)
                            ->get('checks');
            if($q->num_rows() > 0){
                return $q->result()[0];
            }else{
                return NULL;
            }
        }catch(Exception $e){
            return NULL;
        }
    }

    public function fetch_monitor_avg_uptime($monitor_id){
        try{
            $up_time_count  = $this->db->select('count(monitor_id)  as up_time_count')
                            ->where(['monitor_id'=>$monitor_id,'response_code'=>200])
                            ->get('checks')->result()[0]->up_time_count;
                            
            $total_checks= $this->db->select('count(monitor_id)  as total_checks')
                            ->where(['monitor_id'=>$monitor_id])
                            ->get('checks')->result()[0]->total_checks;
            
            if($total_checks == 0)
                return 0;
            else
                return (($up_time_count / $total_checks) *100);
            
        }catch(Exception $e){
            return NULL;
        }
    }



    public function fetch_single_monitor_details($monitor_id){
        try{
            $q = $this->db->select('monitor_id,title,protocol,url,is_active,check_interval,created_at')
                ->where('monitor_id',$monitor_id)
                ->get('monitors');
            if($q->num_rows() > 0){
                return $q->result()[0];
            }else{
                return NULL;
            }
        }catch(Exception $e){
            return NULL;
        }
    }

    public function fetch_monitor_avg_response_time($monitor_id){
        try{
            $q = $this->db->select_avg('response_time','avg_resp_time')
                ->where('monitor_id',$monitor_id)
                ->get('checks');
            if($q->num_rows() > 0){
                return $q->result()[0]->avg_resp_time;
            }else{
                return NULL;
            }
        }catch(Exception $e){
            return NULL;
        }
    }

    public function fetch_monitor_checks($monitor_id){
        try{
            $q = $this->db->select('check_id,monitor_id,response_time,response_code,status,checked_at')
                ->where('monitor_id',$monitor_id)
                ->limit(5)
                ->order_by('checked_at','DESC')
                ->get('checks');
            if($q->num_rows() > 0){
                return $q->result();
            }else{
                return [];
            }
        }catch(Exception $e){
            return [];
        }
    }

    public function fetch_monitor_graph_data($monitor_id){
        try{
            $q = $this->db->select('check_id,response_time,checked_at')
                ->where('monitor_id',$monitor_id)
                ->order_by('checked_at','DESC')
                ->get('checks');
            if($q->num_rows() > 0){
                return $q->result();
            }else{
                return [];
            }
        }catch(Exception $e){
            return [];
        }
    }

    public function fetch_all_monitor_count($user_id){
        try{
             return $this->db->select('count(monitor_id) as totalMonitor')
                            ->where('user_id',$user_id)
                            ->get('monitors')->result()[0]->totalMonitor;

        }catch(Exception $e){
            return 0;
        }
    }
    public function fetch_up_monitor_count($user_id){
        try{
             return $this->db->select('count(monitors.monitor_id) as upMonitor')
                            ->from('last_checks')
                            ->join('monitors','monitors.monitor_id=last_checks.monitor_id')
                            ->where(['user_id'=>$user_id,'status'=>true])
                            ->get()->result()[0]->upMonitor;

        }catch(Exception $e){
            return 0;
        }
    }

    public function fetch_paused_monitor_count($user_id){
        try{
             return $this->db->select_count('user_id')
                            ->where(['user_id'=>$user_id,'is_active'=>false])
                            ->get('monitors')->result()->user_id;

        }catch(Exception $e){
            return 0;
        }
    }

    public function fetch_active_monitor_count($monitor_id){
        try{
             return $this->db->select_count('monitor_id')
                            ->where(['monitor_id'=>$monitor_id,'is_active'=>false])
                            ->get('monitors')->result()->monitor_id;

        }catch(Exception $e){
            return 0;
        }
    }


    public function delete_monitor($monitor_id){
        try{
             return $this->db->where(['monitor_id'=>$monitor_id])
                            ->delete('monitors');

        }catch(Exception $e){
            return false;
        }
    }

    public function fetch_monitor_last_incident($monitor_id){
        try{
            $q = $this->db->select('incident_time')
                            ->where(['monitor_id'=>$monitor_id])
                            ->order_by('incident_time','DESC')
                            ->limit(1)
                            ->get('incidents');
            if($q->num_rows() > 0){
                return $q->result()[0]->incident_time;
            }else{
                return NULL;
            }

       }catch(Exception $e){
           return NULL;
       }
    }

    public function fetch_monitor_incident_count($monitor_id){
        try{
            $q = $this->db->select('count(incident_time) as incident_count')
                            ->where(['monitor_id'=>$monitor_id])
                            ->get('incidents');
            if($q->num_rows() > 0){
                return $q->result()[0]->incident_count;
            }else{
                return NULL;
            }

       }catch(Exception $e){
           return NULL;
       }
    }



}