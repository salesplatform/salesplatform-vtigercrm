<?php
////////////////////////////////////////////////////
// PHPMailer - PHP email class
//
// Class for sending email using either
// sendmail, PHP mail(), or SMTP.  Methods are
// based upon the standard AspEmail(tm) classes.
//
// Copyright (C) 2001 - 2003  Brent R. Matzelle
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * PHPMailer - PHP email transport class
 * @package PHPMailer
 * @author Brent R. Matzelle
 * @copyright 2001 - 2003 Brent R. Matzelle
 */

// SalesPlatform.ru begin
require_once 'includes/SalesPlatform/NetIDNA/idna_convert.class.php';
// SalesPlatform.ru end

//file modified by richie


require("class.smtp.php");
require("class.phpmailer.php");
require_once 'include/utils/CommonUtils.php';

// SalesPlatform.ru begin
function sendmail($to,$from,$subject,$contents,$mail_server,$mail_server_username,$mail_server_password,$filename,$smtp_auth='',$mail_server_port=25,$mail_server_tls='no',$use_sendmail="false")
//function sendmail($to,$from,$subject,$contents,$mail_server,$mail_server_username,$mail_server_password,$filename,$smtp_auth='')
// SalesPlatform.ru end
{
  $mail = new PHPMailer();
        // SalesPlatform.ru begin
	$idn = new idna_convert();
        // SalesPlatform.ru end
  $mail->Subject = $subject;
	$mail->Body    = $contents;//"This is the HTML message body <b>in bold!</b>";

	$initialfrom = $from;

    // SalesPlatform.ru begin
	//$mail->IsSMTP();                                      // set mailer to use SMTP
    // SalesPlatform.ru end
	//$mail->Host = "smtp1.example.com;smtp2.example.com";  // specify main and backup server
	$mail->Host = $mail_server;  // specify main and backup server
	if($smtp_auth == 'true')
		$mail->SMTPAuth = true;
	else
		$mail->SMTPAuth = false;
        // SalesPlatform.ru begin
        if(!empty($use_sendmail) && $use_sendmail != 'false')
            $mail->IsSendmail();
        else
            $mail->IsSMTP();

	if(!empty($mail_server_tls) && $mail_server_tls != 'no')
            $mail->SMTPSecure = $mail_server_tls;

        if(!empty($mail_server_port) && $mail_server_port != 0)
            $mail->Port = $mail_server_port;
        // SalesPlatform.ru end
        
	$mail->Username = $mail_server_username ;//$smtp_username;  // SMTP username
	$mail->Password = Vtiger_Functions::fromProtectedText($mail_server_password);//$smtp_password; // SMTP password
// SalesPlatform.ru begin
	$mail->From = $idn->encode($from);
//	$mail->From = $from;
// SalesPlatform.ru end
	$mail->FromName = $initialfrom;
// SalesPlatform.ru begin
	$mail->AddAddress($idn->encode($to));                  // name is optional
	$mail->AddReplyTo($idn->encode($from));
//	$mail->AddAddress($to);                  // name is optional
//	$mail->AddReplyTo($from);
// SalesPlatform.ru end
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->IsHTML(true);                                  // set email format to HTML

	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
//	$mail->Sender= getReturnPath($mail->Host);
	if(!$mail->Send())
	{
	   echo "Message could not be sent. <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	   exit;
	}

}
?>
