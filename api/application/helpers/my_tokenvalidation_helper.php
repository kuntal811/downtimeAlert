<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validateToken(){

    if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
        $auth = $_SERVER["HTTP_AUTHORIZATION"];
        $auth_array = explode(" ", $auth);
        try{
            $decoded = JWT::decode( $auth_array[1], new Key(SECRET_KEY, 'HS256') );
            $decoded = (array)$decoded;


            return $decoded;

        }catch(Exception $e){
            //print_r($e->getMessage());

            return false;
        }
        }else{
            return false;
        }
}
?>