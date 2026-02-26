<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?PHP
	error_reporting(E_ALL);
	ini_set("display_errors", 1); 
	require_once('phpmailer/class.phpmailer.php');
	
	/*
	unction send() {
$sHeader = 'Content-Type: text/html; charset=utf-8'."\r\n".
'From: '.$this->sFromName.'<'.$this->sFromEmail.'>'."\r\n".
'Reply-To: '.$this->sFromEmail."\r\n".
'X-Mailer: PHP/'.phpversion();

if(mail($this->sToEmail, $this->sSubject, $this->sBody, $sHeader))
return true;
else
return false;
} 
*/	
/*
 	$to = "h197969@yahoo.com";
	$subject = "Trả lời câu hởi của bạn";
	$msg = "Làm như thế nào \r\n <b> Làm sao cũng được </b>";
	
	*/	
		$fh = fopen("artseed.html", 'r');
		$template = fread($fh, filesize("artseed.html"));
		fclose($fh);
	
		$template = '
			vao roi hâhhahahah 2222
		';
		$to = "h197969@yahoo.com";
		//$header = 'MIME-Version: 1.0' . "<br />" . 'Content-type: text/plain; charset=UTF-8' . "<br />";
		//$msg = 'Trả lời câu hỏi '."<br />".' ai biết trả lời sao <br /> <img alt="hoatuoisaigon" title="hoatuoisaigon" src="/upload/hoa-sai-gon/tinh-yeu-hoa-gio.jpg">';
		$subject = "=?UTF-8?B?".base64_encode("nguyễn minh hải 321444").'?=' ;
		//$header = 'làm thế nào : Làm sao cũng được';
		//$header = 'MIME-Version: 1.0' . "<br />" . 'Content-type: text/plain; charset=UTF-8' . "<br />";
  		//mail($to, "=?UTF-8?B?".base64_encode($subject).'?=', $message, $header_ . $header);
	
	
	//$mailcc = "h197969@yahoo.com";
	//$mailcc1 = "";
	$mailsend = sendmail("Seoworld.vn",$to,$subject,$template,$mailcc="",$mailcc1="");
	
	if($mailsend) echo "send mail complate.";
	else echo "mail not sent";
	
	function sendmail($user,$email,$subj,$mess,$mailcc="",$mailcc1="")
	{
		include("email_config.php");
		$mail = new PHPMailer();
		
		/////////goi cho gmail	
	
		$mail->IsSMTP(); // send via SMTP
		$mail->SMTPAuth = true; // turn on SMTP authentication
		
		$mail->SMTPDebug = 1;
		$mail->SMTPSecure = 'tls';
		$mail->Port       = 587;
		$mail->Host = SMTP_SERVER;
		$mail->Username = MAIL_USER; // SMTP username
		$mail->Password = MAIL_PASS; // SMTP password
		//$mail->SetFrom('sales@bachhai.com', 'Bach Hai');
		$mail->CharSet = "UTF-8"; 
		
		$mail->From = MAIL_FROM;
		$mail->FromName = MAIL_FROMNAME;
		
		
		$mail->AddAddress($email,$user);
		
		
		
		$mail->WordWrap = 50; // set word wrap
		
		$mail->IsHTML(true); // send as HTML
		
		$mail->Subject = $subj;
		$mail->Body = $mess;
	/*
		$mail->IsSMTP(); // send via SMTP
		$mail->SMTPAuth = true; // turn on SMTP authentication
		
		$mail->SMTPDebug = 1;
		//$mail->SMTPSecure = 'tls';
		$mail->Host = SMTP_SERVER;
		$mail->Port       = 25;
		$mail->Username = MAIL_USER; // SMTP username
		$mail->Password = MAIL_PASS; // SMTP password
		$mail->CharSet = "UTF-8"; 
		
		$mail->From = MAIL_FROM;
		$mail->FromName = MAIL_FROMNAME;
		$mail->AddAddress($email,$user);
		
		if($mailcc)
			$mail->AddCC($mailcc,"admin");
		if($mailcc1)
			$mail->AddCC($mailcc1,"admin2");
			
		$mail->AddAttachment("att/no-img-luat.jpg");
		
		$mail->WordWrap = 50; // set word wrap
		
		$mail->IsHTML(true); // send as HTML
		
		$mail->Subject = $subj;
		$mail->Body = $mess;
	*/
	

		
		$send=$mail->Send();
		if ($send) {
			return true;
		}else return false;
	}

?>

</body>
</html>



