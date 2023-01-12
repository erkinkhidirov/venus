<?php

/**
 * Venus - PHP SMTP Mailing Service
 * @author    Erkin Khidirov
 */


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require ERK_ROOT . 'phpmailer/Exception.php';
require ERK_ROOT . 'phpmailer/PHPMailer.php';
require ERK_ROOT . 'phpmailer/SMTP.php';

class ErkSmtp{
    protected $mail;
    protected $smtp_host;
    protected $smtp_username;
    protected $smtp_password;
    protected $smtp_port;
    protected $limit_recipients_in_message;

    public function __construct($data)
    {
        $this->smtp_host = $data['smtp_host'];
        $this->smtp_username = $data['smtp_username'];
        $this->smtp_password = $data['smtp_password'];
        $this->smtp_port = $data['smtp_port'];
        $this->limit_recipients_in_message = $data['limit_recipients_in_message'];
    }

    public function smtp_init(Array $recipents, Array $content){

        if(isset($recipents['to']) && !empty($recipents['to'])){

            $recipients = $recipents['to'];
            $devided_recipients = array();

            // Так как в почтовых сервисах есть лимиты на то что в одном письме должно быть столько то получателей поэтому мы делим целый массив юеров на части
            if(count($recipients) > $this->limit_recipients_in_message){
                $devided_recipients = array_chunk($recipents['to'], $this->limit_recipients_in_message);
            } else {
                $devided_recipients[0] = $recipients;
            }

            foreach ($devided_recipients as $recipients){
                $recipents['to'] = $recipients;

                $this->smtp_connect();
                $this->set_recipients($recipents); // Получатели array()
                $this->set_content($content);
                $this->send();
                sleep(1);
            }
        }
    }

    public function smtp_connect(){
        $this->mail = new PHPMailer(true);

        try {
            //Server settingsы
            $this->mail->SMTPDebug = 2;                      //Enable verbose debug output
            $this->mail->CharSet = 'UTF-8';                     //Enable verbose debug output
            $this->mail->isHTML(true);                    //Enable verbose debug output
            $this->mail->isSMTP(true);                                            //Send using SMTP
            $this->mail->Host       = $this->smtp_host;                     //Set the SMTP server to send through
            $this->mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $this->mail->Username   = $this->smtp_username;                     //SMTP username
            $this->mail->Password   = $this->smtp_password;                               //SMTP password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $this->mail->Port       = $this->smtp_port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $this->mail->Debugoutput = function($str, $level) {
                file_put_contents(ERK_ROOT . 'log/smtp.log', gmdate('Y-m-d H:i:s'). "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
            };

        } catch (Exception $e) {
        }
    }

    public function set_recipients(Array $recipents){
        if(!empty($recipents)){
            //Recipients
            $this->mail->setFrom($recipents['from'], $recipents['who']);

            if(!empty($recipents['to']))
            {
                foreach ($recipents['to'] as $address){

                    print_r($address);

                    $this->mail->addAddress($address);
                }
            }

            $this->mail->addReplyTo($recipents['from'], $recipents['who']);
            //$this->mail->addCC('cc@example.com');
            //$this->mail->addBCC('bcc@example.com');
        }
    }

    public function set_attachments(Array $files){
        //Attachments
        if(isset($content['files']) && !empty($content['files'])) {
            foreach ($files as $filename) {
                $this->mail->addAttachment($filename);
            }
        }
    }

    public function set_content(Array $content){
        if(!empty($content)){

            //Content
            $this->mail->isHTML(true); //Set email format to HTML
            $this->mail->Subject = $content['subject'];
            $this->mail->Body    = $content['content'];

            //Attachments
            $this->set_attachments($content['files']);

            //$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        }
    }

    public function send(){
        try {
            $this->mail->send();
        } catch (Exception $e) {
        }
    }
}