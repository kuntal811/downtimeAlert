<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct(){

		parent::__construct();

	}
	public function index(){
        /*
        print_r(date('Y-m-d H:i:s'));
        print_r(DateTimeZone::listIdentifiers()[250]);*/

        var_dump(getenv('SENDGRID_API_KEY'));
    }
}
    ?>