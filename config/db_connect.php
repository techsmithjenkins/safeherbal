<?php
// db_connect.php - Database connection settings
include_once __DIR__ . '/config.php';

class Database {
    private $host = 'localhost'; // Update if different
    private $db_name = 'fredyherbal_db';
    private $username = 'root'; // Update with your MySQL username
    private $password = ''; // Update with your MySQL password
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            if (DEBUG) {
                $this->conn->set_charset("utf8mb4");
            }
        } catch (Exception $e) {
            if (DEBUG) {
                die("Error: " . $e->getMessage());
            } else {
                die("Something went wrong. Please try again later.");
            }
        }
        return $this->conn;
    }
}

// Instantiate and get connection
$db = new Database();
$conn = $db->getConnection();
?>