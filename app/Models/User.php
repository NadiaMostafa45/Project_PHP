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

    public function getAll($search = null) {

    if ($search) {

        $query = "SELECT * FROM users 
                  WHERE name LIKE ? OR email LIKE ?
                  ORDER BY id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute(["%$search%", "%$search%"]);

    } else {

        $query = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function delete($id){

    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $this->db->prepare($query);

    return $stmt->execute([$id]);
}

}