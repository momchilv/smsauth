<?php
require_once(__DIR__ . "/../db/Models/Verifications.php");
require_once(__DIR__ . "/../db/Models/VerificationLogs.php");

define('VERIFICATION_CODE_LENGTH', 6);

class VerificationCode
{
    public static function verifyCode($post_params)
    {
        try {
            $db = new Database();
            $db->getConnection()->beginTransaction();
            if (!isset($post_params['token'])) {
                return array('error_msg' => 'Something went wrong.', 'error_field' => 'general');
            }
            $users_Model = new Model_Users();
            $user = $users_Model->getUserByAccessToken($post_params['token']);
            if (!$user) {
                return array('error_msg' => 'Something went wrong.', 'error_field' => 'general');
            }
            $verifications_Model = new Model_Verifications();
            $verification = $verifications_Model->getVerificationByUser($user['id']);

            $verifications_logs_Model = new Model_VerificationLogs();
            $verificationCode = preg_replace('/[^0-9.]/', '', $post_params['verification_code']);
            if (strlen($verificationCode) != VERIFICATION_CODE_LENGTH || $verificationCode != $verification['code']) {
                $verifications_logs_Model->addLog($user['id'], $verificationCode, 0);
                $wrongAttempts = $verifications_logs_Model->getUserAllWrongAttempsCount($user['id']);
                $db->getConnection()->commit();
                $db->closeConnection();
                if ($wrongAttempts['attemps'] % 3 == 0) {
                    return array('error_msg' => 'Wrong verification code. Please wait 1 minute before try again.', 'verification_code_cooldown' => 1, 'error_field' => 'verification_code');
                } else {
                    return array('error_msg' => 'Wrong verification code', 'error_field' => 'verification_code');
                }
            } else {
                $verifications_logs_Model->addLog($user['id'], $verificationCode, 1);
                $affectedRows = $users_Model->setUserAsActive($user['id']);
                $db->getConnection()->commit();
                $db->closeConnection();
                if ($affectedRows == 1) {
                    Sms::send($user['phone'], 'Welcome to SMSBump!');
                    return array('success' => 1);
                } else {
                    return array('error_msg' => 'Something went wrong.', 'error_field' => 'general', 'description' => 'Fail to set user as active.');
                }
            }
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $db->closeConnection();
            return array('error_msg' => 'Something went wrong.', 'error_field' => 'general', 'description' => $e->getMessage());
        }
    }

    public static function resendCode($post_params)
    {
        try {
            $db = new Database();
            $db->getConnection()->beginTransaction();
            if (!isset($post_params['token'])) {
                return array('error_msg' => 'Something went wrong.', 'error_field' => 'general');
            }
            $users_Model = new Model_Users();
            $user = $users_Model->getUserByAccessToken($post_params['token']);
            if (!$user) {
                return array('error_msg' => 'Something went wrong.', 'error_field' => 'general');
            }
            $verificationCode = rand(100000, 999999);
            $verifications_Model = new Model_Verifications();
            $affectedRows = $verifications_Model->updateUserVerification($user['id'], $verificationCode);
            $db->getConnection()->commit();
            $db->closeConnection();
            if ($affectedRows == 1) {
                // Send SMS
                Sms::send($user['phone'], $verificationCode);
                return array('verification_code_resent' => 1);
            }
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $db->closeConnection();
            return array('error_msg' => 'Something went wrong.', 'error_field' => 'general', 'description' => $e->getMessage());
        }
    }
}
