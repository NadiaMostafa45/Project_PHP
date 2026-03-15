<?php

namespace App\Models;

use Config\Database;
use PDO;

class User {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($name, $email, $password, $role, $roomNo, $ext, $image) {

        $query = "INSERT INTO users (name, email, password, role, room_no, ext, image)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([$name, $email, $password, $role, $roomNo, $ext, $image]);
    }

}