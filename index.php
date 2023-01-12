<?php

/**
 * Venus - PHP SMTP Mailing Service
 * @author    Erkin Khidirov
 */

define("ERK_ROOT", __DIR__ . '/');

require_once(ERK_ROOT . "inc/ErkSmtp.php");

$data = array(
    'smtp_host' => 'smtp.site.com',
    'smtp_username' => 'example@site.com',
    'smtp_password' => 'change_password_here',
    'smtp_port' => 465, // 465 PORT SSL SMTP PORT or use 25 PORT NON SSL
    'limit_recipients_in_message' => 25 // Limit recipients for one message
);

$recipents = array(
    'from' => 'example@site.com', // From Who Email
    'who' => 'Sender', // Name of sender
    'to' => array( // Email Addresses Array
//        'example@site.com',
//        'example2@site.com',
    )
);

$content = array(
    'subject' => 'Letter Subject', // Subject for letter
    'content' => 'Letter Content', // Content for letter HTNL or Text
    'files' => array( // Files Urls Array
//        '/var/path_to_file/example.jpg',
//        '/var/path_to_file/example.zip',
    )
);

$vi_mail = new ErkSmtp($data);
$vi_mail->smtp_init($recipents, $content);
