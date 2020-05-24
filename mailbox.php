<?php

// sonoff-mailbox
//
// Script which saves to log and send e-mail when the postman opens the mailbox.
// 1) When the mailbox opens, the 433MHz receiver from Sonoff ("door window alarm" device) sends the message to RF Bridge from Sonoff.
// 2) RF Bridge, connected to local WiFi network, sends the notification to MQTT server installed on NAS Synology.
// 3) Bish-bosh installed on NAS Synology reads the notification and sends HTTP request to this script.

/***********
* Settings *
***********/

// email settings
$email_sending = "enabled"; // send e-mails or no? "enabled" or "disabled"
$email_smtp_server = "mail.host.com"; // SMTP server to send e-mails
$email_from = "ItIsMe"; // sender of e-mail (arbitrary name of sender)
$email_smtp_user = "user@host.com"; // sender of e-mail (SMTP server login)
$email_smtp_password = "password"; // sender's password (SMTP server password)
$email_smtp_port = "587"; // SMTP port
$emails = array('user@host.com','user2@host.com'); // e-mail addresses to send e-mails
$email_subject = "New letter in mailbox"; // e-mail's subject

// other settings
$curl = "/usr/local/bin/curl"; // path to curl (check it by executing "which curl" as user www)
$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'); // names of week days in your language

/************
* Main part *
************/

// include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// get info
$date = date('Y.m.d');
$time = date('H:i:s');
$day = $days[date('w')];

// save information about last visit
$msg = "$date<br><b>$day<br>$time</b>";
unlink('logs/last.log');
$log_file = fopen('logs/last.log', 'w');
fwrite($log_file, $msg);
fclose($log_file);

// save to log
$msg = "$date $time\n";
$log_file = fopen('logs/mailbox.log', 'a');
fwrite($log_file, $msg);
fclose($log_file);

// rotate logs to last 1000 lines
$log_content = `tail -1000 logs/mailbox.log`;
unlink('logs/mailbox.log');
$log_file = fopen('logs/mailbox.log', 'w');
fwrite($log_file, $log_content);
fclose($log_file);

// send e-mails
if ($email_sending == "enabled") {

    // send e-mail
    foreach($emails as $email)
    {
	$mail = new PHPMailer(true);
	$mail->SMTPDebug = 0;

	$mail->isSMTP();
        $mail->Host       = "$email_smtp_server";
        $mail->SMTPAuth   = true;
        $mail->Username   = "$email_smtp_user";
        $mail->Password   = "$email_smtp_password";
        $mail->SMTPSecure = 'tls';
        $mail->Port       = "$email_smtp_port";

	$mail->SMTPOptions = array(
	    'ssl' => array(
	    'verify_peer' => false,
	    'verify_peer_name' => false,
	    'allow_self_signed' => true
	    )
	);

        //Recipients
        $mail->setFrom("$email_smtp_user", "$email_from");
        $mail->addAddress("$email");
	
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "$email_subject";
        $mail->Body    = "$date, $day, godz. $time";
        $mail->send();
    }
}

?>
