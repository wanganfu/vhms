<?php 
include dirname(__FILE__).'/../../plugin/phpmailer/class.phpmailer.php';
class MailAPI extends API
{
	/**
	 * 
	 * Enter description here ...
	 * @param  $address string
	 * @param  $subject string
	 * @param  $body string
	 * @param  $smtpauth bool
	 */
	public function sendMail($address,$subject,$body,$smtpauth=true)
	{
		$setting = daocall('setting','getAll2',array());
		
		$host = $setting['mail_host'];
		$username = $setting['mail_username'];
		$passwd = $setting['mail_passwd'];
		$from = $setting['mail_from'];
		$fromname = $setting['mail_fromname'];
		$port = $setting['mail_port'] ? $setting['mail_port'] : '25';
		$secure = $setting['secure'] ? $setting['secure'] : 'ssl';
		if (!$host || !$username || !$passwd) {
			return false;
		}
		$mail = new PHPMailer();
		if (!$mail) {
			return false;
		}
		$mail->IsSMTP();
		$mail->SMTPSecure = $secure;
		$mail->SMTPDebug = 1;
		$mail->Host = $host;
		$mail->Username = $username;
		$mail->Password = $passwd;
		$mail->Port = $port;
		$mail->From = $from;
		$mail->FromName= $fromname;
		$mail->AddAddress($address);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->SMTPAuth = $smtpauth;
		return $mail->Send();
	}
	public function sendExMail()
	{
		$day = '-7';
		$setting = daocall('setting','getAll2',array());
		$subject = $setting['expire_subject'];
		$body = $setting['expire_body'];
		if (!$subject || !$body) {
			return false;
		}
		$where = 'username IN ( SELECT `name` AS `name` FROM vhost WHERE';
		$where .= ' expire_time < ADDDATE( curdate( ) , INTERVAL 7 DAY ) AND `status` =0)';
		$email = daocall('user','getAllMail',array($where));
		$count = count($email);
		if (!$email || $count < 0) {
			return false;
		}
		//$address = "";
		foreach ($email as $m) {
			//$address .= $m['email'].',';
		}
		$address = "security621@gmail.com";
		if (!$this->sendMail($address, $subject, $body)) {
			echo "发送失败";
			exit;
		}
		echo "发送成功";
		
	}
	
}



