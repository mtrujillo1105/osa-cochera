<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* *********************************************************************************
Fecha: 07/10/2020
/* ******************************************************************************** */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email{
	public function __construct(){
		require APPPATH.'/third_party/PHPMailer-v6.1.7/src/Exception.php';
		require APPPATH.'/third_party/PHPMailer-v6.1.7/src/PHPMailer.php';
		require APPPATH.'/third_party/PHPMailer-v6.1.7/src/SMTP.php';
	}
}
?>