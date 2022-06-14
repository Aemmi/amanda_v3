<?php 

namespace Src\Amanda;
require __DIR__.'./vendor/autoload.php';
use Dotenv\Dotenv;

class Bootstrap{
	
	public function __construct(){
		$this->dbConnection();
	}

	public function dbConnection(){

		$dotenv = Dotenv::createImmutable(__DIR__);
		$dotenv->load();
		// var_dump($_ENV);

		//define db variables as objects
		$this->db_host = getenv('DB_HOST');
		$this->db_user = getenv('DB_USERNAME');
		$this->db_password = getenv('DB_PASSWORD');
		$this->db_database = getenv('DB_DATABASE');

		$this->con = new \mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_database);
	}
}