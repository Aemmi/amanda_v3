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
    private $sql;
    private $result;

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
    public function count($sql, $params = []) {
        $this->sql = $sql;

        try {
            // Check if the query contains placeholders (?)
            if (strpos($sql, '?') !== false && !empty($params)) {
                // This is a prepared statement with placeholders
                $stmt = $this->con->prepare($sql);

                // Bind the parameters dynamically
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value); // Bind parameters starting from position 1
                }

                // Execute the prepared statement
                $stmt->execute();
            } else {
                // This is a regular query without placeholders
                $stmt = $this->con->query($sql);
            }

            // Return the number of rows
            return $stmt->rowCount();

        } catch (PDOException $e) {
            return 0; // Return 0 if an error occurs
        }
    }

    public function sanitize($value) {
        $value = trim($value);
        $value = htmlspecialchars($value);
        $value = $this->con->quote($value);

        return $value;
    }

    // Function to insert data into the db
    public function insert($sql, $params = []) {
        $this->sql = $sql;
    
        try {
            // Check if the query contains placeholders (?)
            if (strpos($sql, '?') !== false && !empty($params)) {
                // This is a prepared statement with placeholders
                $stmt = $this->con->prepare($this->sql);
    
                // Bind the parameters dynamically
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value); // Bind parameters starting from position 1
                }
    
                // Execute the prepared statement
                $stmt->execute();
            } else {
                // This is a regular query without placeholders
                $query = $this->con->exec($this->sql);
                if ($query === false) {
                    return 0; // Return 0 if the query execution failed
                }
            }
    
            return 1; // Return 1 if successful
    
        } catch (PDOException $e) {
            return 0; // Return 0 if there is an exception
        }
    }    

    // Function to perform selection queries from db
    public function select($sql, $params = []) {
        $this->sql = $sql;
        $this->result = array();
    
        try {
            // Check if the query contains placeholders (?)
            if (strpos($sql, '?') !== false && !empty($params)) {
                // This is a prepared statement with placeholders
                $stmt = $this->con->prepare($this->sql);
    
                // Bind the parameters dynamically
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value); // Bind parameters starting from position 1
                }
    
                $stmt->execute();
            } else {
                // This is a regular query without placeholders
                $stmt = $this->con->query($this->sql);
            }
    
            // Fetch results
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->result[] = $row;
            }
    
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    
        return $this->result;
    }
    

    public function find($tbl, $col)
    {
        // Validate table and column format
        if (strpos($tbl, '.') === false) {
            throw new \Exception("Invalid table.column format: $tbl");
        }

        // Extract table and column
        [$table, $column] = explode('.', $tbl);

        // Sanitize table and column names
        $allowedTables = ['users', 'posts', 'products']; // Example whitelist
        $allowedColumns = ['id', 'email', 'username']; // Example whitelist

        if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
            throw new \Exception("Invalid table or column name.");
        }

        // Build the query
        $this->sql = "SELECT * FROM $table WHERE $column = :col LIMIT 1";

        $this->result = [];

        try {
            // Prepare statement
            $stmt = $this->con->prepare($this->sql);

            // Bind parameter and execute
            $stmt->bindParam(':col', $col, PDO::PARAM_STR);
            $stmt->execute();

            // Fetch results
            $this->result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Log error and optionally rethrow
            error_log($e->getMessage());
            throw new \Exception("Database error occurred.");
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
    public function destroy($sql, $params = []) {
        try {
            // Check if the query contains placeholders (?)
            if (strpos($sql, '?') !== false && !empty($params)) {
                // This is a prepared statement with placeholders
                $stmt = $this->con->prepare($sql);

                // Bind the parameters dynamically
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value); // Bind parameters starting from position 1
                }

                // Execute the prepared statement
                $stmt->execute();
            } else {
                // This is a regular query without placeholders
                $stmt = $this->con->exec($sql);
                if ($stmt === false) {
                    return 0; // Return 0 if the query execution failed
                }
            }

            return 1; // Return 1 if successful

        } catch (PDOException $e) {
            return $e->getMessage(); // Return error message in case of an exception
        }
    }

    // Update query method
    public function update($sql, $params = []) {
        try {
            // Check if the query contains placeholders (?)
            if (strpos($sql, '?') !== false && !empty($params)) {
                // This is a prepared statement with placeholders
                $stmt = $this->con->prepare($sql);

                // Bind the parameters dynamically
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key + 1, $value); // Bind parameters starting from position 1
                }

                // Execute the prepared statement
                $stmt->execute();
            } else {
                // This is a regular query without placeholders
                $stmt = $this->con->exec($sql);
                if ($stmt === false) {
                    return 0; // Return 0 if the query execution failed
                }
            }

            return 1; // Return 1 if successful

        } catch (PDOException $e) {
            return $e->getMessage(); // Return error message in case of an exception
        }
    }

}

