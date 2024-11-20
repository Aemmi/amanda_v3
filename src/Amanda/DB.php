<?php

// Create the project class for interacting with the database
namespace Src\Amanda;
use PDO;
use PDOException;

class DB {
    // Construct the class
	private $db_host;
    private $db_user;
    private $db_password;
    private $db_database;
    private $con = null;

    public function __construct() {
        // Define db variables as objects
        $this->db_host = $_ENV['DB_HOST'];
        $this->db_user = $_ENV['DB_USERNAME'];
        $this->db_password = $_ENV['DB_PASSWORD'];
        $this->db_database = $_ENV['DB_DATABASE'];

        $dsn = "mysql:host={$this->db_host};dbname={$this->db_database}";

        try {
            $this->con = new PDO($dsn, $this->db_user, $this->db_password);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit;
        }
    }

    public function connector() {
        return $this->con;
    }

    // Function to check connection error
    public function testConnection() {
        if ($this->con) {
            return "Connection was successful!";
        } else {
            return "Sorry, connection failed";
        }
    }

    public function connection_error($sql) {
        try {
            $query = $this->con->query($sql);
            if (!$query) {
                return $this->con->errorInfo();
            } else {
                return "No query error!";
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    // Function to count number of rows from a query
    public function countRows($sql) {
        $this->sql = $sql;

        try {
            $stmt = $this->con->query($this->sql);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function sanitize($value) {
        $value = trim($value);
        $value = htmlspecialchars($value);
        $value = $this->con->quote($value);

        return $value;
    }

    // Function to insert data into the db
    public function insert($sql) {
        $this->sql = $sql;

        try {
            $query = $this->con->exec($this->sql);
            if ($query === false) {
                return 0;
            } else {
                return 1;
            }
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Function to perform selection queries from db
    public function select($sql) {
        $this->sql = $sql;
        $this->result = array();

        try {
            $stmt = $this->con->query($this->sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->result[] = $row;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }

        return $this->result;
    }

    public function find($tbl, $col) {
        $data = explode('.', $tbl);

        $table = $data[0];
        $column = $data[1];

        $this->sql = "SELECT * FROM $table WHERE $column = :col LIMIT 1";

        $this->result = array();

        try {
            $stmt->bindParam(':col', $col, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->result[] = $row;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }

        return $this->result;
    }

    public function search($tbl, $col) {
        $data = explode('.', $tbl);

        $table = $data[0];
        $column = $data[1];

        $this->sql = "SELECT * FROM $table WHERE $column LIKE :col";

        $this->result = array();

        try {
            $stmt = $this->con->prepare($this->sql);
            $colValue = '%' . $col . '%';
            $stmt->bindParam(':col', $colValue, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->result[] = $row;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }

        return $this->result;
    }

    // Delete query method
    public function destroy($sql) {
        try {
            $stmt = $this->con->prepare($sql);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    // Update query method
    public function update($sql) {
        try {
            $stmt = $this->con->prepare($sql);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}

