<?php

//create the project class for interacting with the database
namespace Src\Amanda;

class DB{
	//contruct the class
	
	private $con = null;

	public function __construct(){
		//define db variables as objects
		$this->db_host = $_ENV['DB_HOST'];
		$this->db_user = $_ENV['DB_USERNAME'];
		$this->db_password = $_ENV['DB_PASSWORD'];
		$this->db_database = $_ENV['DB_DATABASE'];

		$this->con = new \mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_database);
	}

	public function connector(){
		return $this->con;
	}

	//function to check connection error
	public function testConnection(){
		if($this->con->connect_error){
			return "Sorry, connection failed";
		}else{
			return "Connection was successful!";
		}
	}
    
    public function connection_error($sql){
        $query = $this->con->query($sql);
        if(!$query){
            return $this->con->error;
        }else{
            return "No query error!";
        }
    }


	//function to count number of rows from a query
	public function countRows($sql){

		$this->sql = $sql;
		
		try{
			$this->count = $this->con->query($this->sql);
			return $this->count->num_rows;
		}catch(Exception $e){
			return 0;
		}
		
	}


	//function to insert data into the db
	public function insert($sql){
		
		$this->sql = $sql;

		$query = $this->con->query($this->sql);

		if(!$query){
			//return false
			return 0;
		}else{
			return 1;
		}
	}

	//function to perform selection queries from db
	public function select($sql){
		$this->sql = $sql;

		$this->result = array();

		$this->query = $this->con->query($this->sql);

		while($row = $this->query->fetch_assoc()){

			//get data into multi-dimensional array
			$this->result[] = $row;

		}

		return $this->result;

	}

	//=======================================
	//delete query method
	public function delete($sql){
		$status;
		$this->sql = $sql;
		$this->query = $this->con->query($this->sql);
		if(!$this->query){
			$status = 0;
			return $status;
		}else{
			$status = 1;
			return $status;
		}
	}
	
	//=======================================
	//delete query method
	public function update($sql){
		$status;
		$this->sql = $sql;
		$this->query = $this->con->query($this->sql);
		if(!$this->query){
			$status = 0;
			return $status;
		}else{
			$status = 1;
			return $status;
		}
	}

}