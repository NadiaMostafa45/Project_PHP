<?php

namespace App\Models;

use Config\Database;
use PDO;

class User {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($name, $email, $password, $role, $ext, $image) {

        $query = "INSERT INTO users (name, email, password, role, ext, image)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([$name, $email, $password, $role, $ext, $image]);
    }

    public function emailExists($email) {
        $query = "SELECT 1 FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);

        return (bool) $stmt->fetchColumn();
    }

    public function emailExistsForOtherUser($email, $id) {
        $query = "SELECT 1 FROM users WHERE email = ? AND id != ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email, $id]);

        return (bool) $stmt->fetchColumn();
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

    public function getById($id) {
        $query = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $email, $role, $ext, $image = null, $password = null) {
        $fields = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'ext' => $ext,
        ];

        if ($image !== null) {
            $fields['image'] = $image;
        }

        if ($password !== null) {
            $fields['password'] = $password;
        }

        $setParts = [];
        $values = [];
        foreach ($fields as $column => $value) {
            $setParts[] = "$column = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $query = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);

        return $stmt->execute($values);
    }

public function delete($id){

    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $this->db->prepare($query);

    return $stmt->execute([$id]);
}

}