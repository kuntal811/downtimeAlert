<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Error messages
 */

defined("REQUIRED") ? null: define("REQUIRED"," is required");
defined("MIN_LENGTH") ? null: define("MIN_LENGTH"," must be greater than ");
defined("MAX_LENGTH") ? null: define("MAX_LENGTH"," must be less than ");
defined("INVALID_EMAIL") ? null: define("INVALID_EMAIL","Enter valid email");

/**
 * 
 * @param mixed  $data      Data to be validated
 * @param  array $option    Validation citeria
 * 
 * @return array Associative array containing
 */

$minNameLength = 3;
$maxNameLength = 16;

$minPasswordLength = 5;
$maxPasswordLength = 20;

if ( ! function_exists('validate_input')){
   function validate_input($data,$inputType){
        $minNameLength = 3;
        $maxNameLength = 16;
        
        $minPasswordLength = 5;
        $maxPasswordLength = 20;

        $error = [];

       switch($inputType){

        case 'email':
            if(!$data)
                array_push($error,$inputType.REQUIRED);

            if (!filter_var($data, FILTER_VALIDATE_EMAIL))
                array_push($error,INVALID_EMAIL);
            break;

        case 'name':
            if (!preg_match("/^[a-zA-Z-']*$/",$data))
                array_push($error,$inputType.REQUIRED);

            if (! (strlen($data) >= $minNameLength))
                array_push($error,$inputType.MIN_LENGTH.($minNameLength-1));

            if (! (strlen($data) <= $maxNameLength))
                array_push($error,$inputType.MAX_LENGTH.($maxNameLength+1));
            break;

        case 'passsword':
            if (! (strlen($data) >= $minPasswordLength))
                array_push($error,$inputType.MIN_LENGTH.($minPasswordLength-1));

            if (! (strlen($data) <= $maxPasswordLength))
                array_push($error,$inputType.MAX_LENGTH.($maxPasswordLength+1));
            break;
       }
       
        return count($error) == 0 ? true : $error;
   }
}

function clean_input_data($data) {

    $data = trim($data);

    $data = stripslashes($data);

    $data = htmlspecialchars($data);

    return $data;
}