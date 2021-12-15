<?php
class User extends CI_Controller{
    public function __construct() {
        parent::__construct();

        $this->load->model('UserModel','user');
    }

    public function login() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $login = $this->user->login($username, $password);

        if(count($login)) {
            $logged_in  = true;
            $token      = $login['access_token'];
        } else {
            $logged_in  = false;
            $token      = '';
        }

        $data = array(
            'logged_in' => $logged_in,
            'token'     => $token
        );

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function check_token($token) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $check_token = $this->user->check_token($token);

        if(count($check_token)) {
            $valid = true;
        } else {
            $valid = false;
        }

        $data = array('valid' => $valid);

        header('Content-Type: application/json');

        echo json_encode($data);
    }

    public function add_team() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $teamName  = $this->input->post('teamName');
        $teamDescription = $this->input->post('teamDescription');


        if($this->user->add_team($teamName, $teamDescription)) {
            $inserted = true;
        } else {
            $inserted = false;
        }

        $data = array('inserted' => $inserted);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get_teams() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $noOfData = $this->input->get('no');
        $offset = $this->input->get('offset');
        $data = $this->user->get_teams($noOfData,$offset);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get_team_details() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');


        $teamId = $this->input->post('id');
        $data = [
            'team'=> $this->user->get_team_details($teamId)[0],
            'players'=> $this->user->get_players()
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get_total_team_count() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $data = $this->user->get_total_team_count();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function delete_team() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $id = $this->input->post('id');
        if($this->user->delete_team($id)) {
            $deleted = true;
        } else {
            $deleted = false;
        }

        $data = array('deleted' => $deleted);

        header('Content-Type: application/json');
        echo json_encode($data);
    }


    public function add_player() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $name  = $this->input->post('name');
        $cat = $this->input->post('cat');
        $isCap  = $this->input->post('isCap');
        $isViceCap = $this->input->post('isViceCap');
        $credit  = $this->input->post('credit');
        $belongToA = $this->input->post('belongToA');
        $belongToB  = $this->input->post('belongToB');
        $teamId  = $this->input->post('team');

        if($this->user->add_player($name, $cat, $isCap, $isViceCap, $credit, $belongToA, $belongToB,$teamId)) {
            $inserted = true;
        } else {
            $inserted = false;
        }

        $data = array('inserted' => $inserted);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get_players() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $data = $this->user->get_players();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function delete_player() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $id = $this->input->post('id');
        if($this->user->delete_player($id)) {
            $deleted = true;
        } else {
            $deleted = false;
        }

        $data = array('deleted' => $deleted);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    
    public function select_player() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $player_id = $this->input->post('player_id');
        $team_id = $this->input->post('team_id');
        if($this->user->select_player($player_id, $team_id)) {
            $selected = true;
        } else {
            $selected = false;
        }

        $data = array('selected' => $selected);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function unselect_player() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');

        $player_id = $this->input->post('player_id');
        $team_id = $this->input->post('team_id');
        if($this->user->unselect_player($player_id, $team_id)) {
            $unselected = true;
        } else {
            $unselected = false;
        }

        $data = array('unselected' => $unselected);

        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
?>