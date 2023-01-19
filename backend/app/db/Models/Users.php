<?php

require_once("BaseModel.php");

class Model_Users extends BaseModel
{
    function __construct()
    {
        parent::__construct('users');
    }

    public function createUser($phone, $email, $password)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $token = base64_encode($phone . rand(1, 1000000) . $hashed_password);
        $query = "INSERT IGNORE INTO {$this->table_name} (`phone`, `email`, `password`, `status`, `access_token`) VALUES (:phone, :email, :hashed_password, 'PENDING', :token);";
        $fields = array('phone' => $phone, 'email' => $email, 'hashed_password' => $hashed_password, 'token' => $token);
        $insertedId = $this->db->insertRow($query, $fields);
        return $this->getUserById($insertedId);
    }

    public function getUserById($id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE `id` = :id";
        $fields = array('id' => $id);
        return $this->db->fetchRow($query, $fields);
    }

    public function getUserByAccessToken($token)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE `access_token` = :token";
        $fields = array('token' => $token);
        return $this->db->fetchRow($query, $fields);
    }

    public function setUserAsActive($user_id)
    {
        $query = "UPDATE `users` SET `status` = 'ACTIVE' WHERE `id` = :user_id";
        $fields = array('user_id' => $user_id);
        return $this->db->updateQuery($query, $fields);
    }
}
