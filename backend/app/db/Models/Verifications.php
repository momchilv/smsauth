<?php
require_once("BaseModel.php");

class Model_Verifications extends BaseModel
{
    function __construct()
    {
        parent::__construct('verifications');
    }

    public function addUserVerification($user_id, $verification_code)
    {
        //insert
        $query = "INSERT INTO `verifications` (`user_id`, `code`, `date_last_sent`) VALUES (:user_id, :verification_code, CURRENT_TIMESTAMP);";
        $fields = array('user_id' => $user_id, 'verification_code' => $verification_code);
        $this->db->insertRow($query, $fields);

        return $this->getVerificationByUser($user_id);
    }

    public function updateUserVerification($user_id, $verification_code)
    {
        $query = "UPDATE `verifications` SET `code` = :verification_code, `date_last_sent` = CURRENT_TIMESTAMP WHERE `user_id` = :user_id AND `date_last_sent` < (now() - INTERVAL 1 MINUTE);";
        $fields = array('user_id' => $user_id, 'verification_code' => $verification_code);
        return $this->db->updateQuery($query, $fields);
    }

    public function getVerificationByUser($user_id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE `user_id` = :user_id";
        $fields = array('user_id' => $user_id);

        return $this->db->fetchRow($query, $fields);
    }
}
