<?php

if(!defined('BASEPATH')) exit('No direct script access allowed!');

class UserModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function create($data) {
        try{
            return $this->db->insert('users',$data);
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


    /*#########################################################################*/
    public function check_token($token) {
        $data = array();

        $this->db->where('access_token', $token);
        $this->db->limit(1);
        $q = $this->db->get('accounts');

        if ($q->num_rows() > 0)
        {
            $data = $q->row_array();
        }

        $q->free_result();
        return $data;
    }
    public function add_team($name, $description) {
        $data = array(
            'name'     => $name,
            'description'    => $description,

        );

        return $this->db->insert('teams', $data);
    }

    public function get_teams($limit, $offset=0) {
        return $this->db->order_by('id','DESC')
                        ->limit($limit,$offset)
                        ->get('teams')->result();
    }

    public function get_team_details($id) {
        return $this->db->select('name,description')->where(['id'=>$id])
                        ->get('teams')->result();
    }
    

    public function get_total_team_count() {
        return $this->db->get('teams')->num_rows();
    }

    public function delete_team($id) {
        return $this->db->delete('teams',['id'=>$id]);
    }

    /*  Players */

    public function add_player($name, $cat, $isCap, $isViceCap, $credit, $belongToA, $belongToB,$teamId) {
        $data = array(
            'name'     => $name,
            'category'  => $cat,
            'is_captain'    => $isCap,
            'is_vice_captain'   => $isViceCap,
            'credit'    => $credit,
            'belong_to_a'   => $belongToA,
            'belong_to_b'   => $belongToB
        );
        $this->db->trans_start();

        $this->db->insert('players', $data);
        if($teamId!='' && $teamId!=NULL){
            $insert_id = $this->db->insert_id();
            $this->db->insert('team_players',['team_id'=>intval($teamId),'player_id'=>$insert_id]);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
        
    }

    public function get_players() {
        return $this->db->select('players.id as player_id,players.name as player_name,players.category,is_captain,is_vice_captain,credit,belong_to_a,belong_to_b,teams.id as team_id,teams.name as team_name',)
                    ->from('team_players')
                    ->join('players','players.id=team_players.player_id','right outer')
                    ->join('teams','teams.id=team_players.team_id','left')
                    ->order_by('player_id','DESC')
                    ->get()->result();
    }

    public function delete_player($id) {
        return $this->db->delete('players',['id'=>$id]);
    }

    public function select_player($player_id,$team_id) {

        return $this->db->insert('team_players',['team_id'=>intval($team_id),'player_id'=>intval($player_id)]);
        
    }
    public function unselect_player($player_id,$team_id) {

        return $this->db->delete('team_players',['team_id'=>intval($team_id),'player_id'=>intval($player_id)]);
        
    }

}