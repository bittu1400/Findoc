<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Auth;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO user_entity (name, email, password, phone, role) 
                VALUES (:name, :email, :password, :phone, :role)";
        
        $params = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Auth::hashPassword($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role']
        ];

        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM user_entity WHERE email = :email LIMIT 1";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM user_entity WHERE user_id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function emailExists($email)
    {
        $sql = "SELECT COUNT(*) as count FROM user_entity WHERE email = :email";
        $result = $this->db->fetch($sql, ['email' => $email]);
        return $result['count'] > 0;
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'password' && $key !== 'user_id') {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE user_entity SET " . implode(', ', $fields) . " WHERE user_id = :id";
        return $this->db->execute($sql, $params) > 0;
    }

    public function updatePassword($id, $newPassword)
    {
        $sql = "UPDATE user_entity SET password = :password WHERE user_id = :id";
        $params = [
            'password' => Auth::hashPassword($newPassword),
            'id' => $id
        ];
        return $this->db->execute($sql, $params) > 0;
    }

    public function verifyCredentials($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        if (Auth::verifyPassword($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function getAll($role = null)
    {
        if ($role) {
            $sql = "SELECT user_id, name, email, phone, role, registered_date 
                    FROM user_entity WHERE role = :role ORDER BY registered_date DESC";
            return $this->db->fetchAll($sql, ['role' => $role]);
        }

        $sql = "SELECT user_id, name, email, phone, role, registered_date 
                FROM user_entity ORDER BY registered_date DESC";
        return $this->db->fetchAll($sql);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM user_entity WHERE user_id = :id";
        return $this->db->execute($sql, ['id' => $id]) > 0;
    }

    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM user_entity";
        $result = $this->db->fetch($sql);
        return $result['count'];
    }

    public function getCountByRole($role)
    {
        $sql = "SELECT COUNT(*) as count FROM user_entity WHERE role = :role";
        $result = $this->db->fetch($sql, ['role' => $role]);
        return $result['count'];
    }
}

?>