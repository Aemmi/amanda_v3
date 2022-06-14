<?php 

	use Dotenv\Dotenv;
	use Src\Amanda\Db;

	require "vendor/autoload.php";

	$dotenv = Dotenv::createImmutable(__DIR__);
	$dotenv->load();
	// var_dump($_ENV);

	$db = (new Db())->connector();

	define('DB', $db);