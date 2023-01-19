<?php
require_once("BaseModel.php");
class Model_VerificationLogs extends BaseModel
{
    function __construct()
    {
        parent::__construct('verification_logs');
    }
    public function addLog($user_id, $code, $valid)
    {
        $code = preg_replace('/[^0-9.]/', '', $code);
        $query = "INSERT INTO {$this->table_name} (`user_id`, `code`, `valid`) VALUES (:user_id, :code, :valid);";
        $fields = array('user_id' => $user_id, 'code' => $code, 'valid' => $valid);
        $this->db->fetchAll($query, $fields);
    }

    public function getUserAllWrongAttempsCount($user_id)
    {
        $query = "SELECT COUNT(`id`) AS 'attemps' FROM {$this->table_name} WHERE `user_id` = :user_id";
        $fields = array('user_id' => $user_id);

        return $this->db->fetchRow($query, $fields);
    }
}
