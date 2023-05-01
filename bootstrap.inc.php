<?php 
	//default timezone
	date_default_timezone_set("Africa/Lagos");
	//load all classes

    // Import PHPMailer classes into the global namespace
    // These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception; 
    
    require 'SMTP/src/Exception.php';
    require 'SMTP/src/PHPMailer.php';
    require 'SMTP/src/SMTP.php';
    
    //smtp mailer starts here
    $mailer = new PHPMailer(true);
	
	use Dotenv\Dotenv;
	use Src\Amanda\DB;

	session_start();

	require "vendor/autoload.php";

	$dotenv = Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	
	if($_ENV['APP_DEBUG'] == "true"){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		// error_reporting(E_All);
	}else{
	    error_reporting(0);
	}

	require "src/functions/functions.php";

	$db = (new Db())->connector();