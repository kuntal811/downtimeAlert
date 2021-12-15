<?php

if(!defined('BASEPATH')) exit('No direct script access allowed!');

use \SendGrid\Mail\From;
use \SendGrid\Mail\To;
use \SendGrid\Mail\Content;
use \SendGrid\Mail\Mail;
use \SendGrid\Mail\Personalization;
use \SendGrid\Mail\ReplyTo;

class MailModel extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function send_mail_by_email($email,  $subject,   $body){
        
        //$email_list=['kuntalsarkar00@gmail.com','mirasarkar010@gmail.com'];
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("20352027@pondiuni.ac.in", "Downtime Alert");
        $email->setSubject($subject);
        $email->addTo($email);
        
        $email->addContent($body);
        /*
        $email->addContent(
            "text/html", "<strong> name and easy to do anywhere, even with PHP</strong>"
        );
        */

        //$email->addSubstitution('name','kuntal');
        //$email->addSubstitution('name','mira');
        $api_key = getenv('SENDGRID_API_KEY');
        $sendgrid = new \SendGrid($api_key);

        echo json_encode($email, JSON_PRETTY_PRINT), "\n";

        /*
        $body = $this->kitchenSink();

        if (!($body instanceof Mail)) {
            echo 'Invalid body to send KitchenSink function', "\n";
            return;
        }
        */
        
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }
    public function sendmail_bulk($email_list){
        
        //$email_list=['kuntalsarkar00@gmail.com','mirasarkar010@gmail.com'];
        $email = new \SendGrid\Mail\Mail(); 
        $email->setFrom("20352027@pondiuni.ac.in", "Downtime Alert");
        $email->setSubject("Website Down Alert!");
        $email->addTo("kuntalsarkar010@gmail.com");
        foreach($email_list as $email_id){
            $email->addBcc($email_id);
        }
        
        $email->addContent("text/plain", "Hey, Your website is down, go to Downtime Alert and check");
        /*
        $email->addContent(
            "text/html", "<strong> name and easy to do anywhere, even with PHP</strong>"
        );
        */

        //$email->addSubstitution('name','kuntal');
        //$email->addSubstitution('name','mira');
        $api_key = getenv('SENDGRID_API_KEY');
        $sendgrid = new \SendGrid($api_key);

        echo json_encode($email, JSON_PRETTY_PRINT), "\n";

        /*
        $body = $this->kitchenSink();

        if (!($body instanceof Mail)) {
            echo 'Invalid body to send KitchenSink function', "\n";
            return;
        }
        */
        
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }

    public function kitchenSink()
    {
        try {
            $from = new From("20352027@pondiuni.ac.in", "Downtime Alert");
            $subject = "Server went down";
            $to = new To("kuntalsarkar00@gmail.com", "Kuntal Sarkar");
            $content = new Content("text/plain", " name some text here");
            $mail = new Mail($from, $to, $subject, $content);
 /*   
            $personalization0 = new Personalization();
            $personalization0->addTo(new To("sourabhsarkar2016@gmail.com", "Sourabh Sarkar"));


            $personalization0->addSubstitution("name", "Sourabh");
            $mail->addPersonalization($personalization0);
*/
   /* 
            $personalization1 = new Personalization();
            $personalization1->addTo(new To("mirasarkar010@gmail.com", "Mira Sarkar"));


            $personalization1->addSubstitution("name", "mira");


            $mail->addPersonalization($personalization1);
    
*/
    
            // Examples of adding personalization by specifying personalization indexes
            /*
            $mail->addCc("test15@example.com", "Example User", null, 0);
            $mail->addBcc("test16@example.com", "Example User", null, 1);
    
            $content = new Content("text/html", "<html><body>some text here</body></html>");
            $mail->addContent($content);
            */
    

            $reply_to = new ReplyTo("20352027@pondiuni.ac.in", "Downtime Alert");
            $mail->setReplyTo($reply_to);
    
            echo json_encode($mail, JSON_PRETTY_PRINT), "\n";
            return $mail;
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    
        return null;
    }
}
?>