<?php
require_once(__DIR__ . "/../db/Models/Users.php");
require_once(__DIR__ . "/../db/Models/Verifications.php");
require_once(__DIR__ . "/../db/Database.php");
require_once(__DIR__ . "/../services/Sms.php");

class Signup
{
    public static function addUser($post_params)
    {

        try {
            $db = new Database();
            $db->getConnection()->beginTransaction();
            [$phone, $email, $password] = self::validateInput($post_params);
            $users_Model = new Model_Users();
            $user = $users_Model->createUser($phone, $email, $password);
            if (!$user) {
                return array('error_msg' => 'User with this phone number already exist.', 'error_field' => 'phone', 'verification_code_sent' => 0);
            }
            $verifications_Model = new Model_Verifications();
            $verificationCode = rand(100000, 999999);
            $verification = $verifications_Model->addUserVerification($user['id'], $verificationCode);
            $db->getConnection()->commit();
            $db->closeConnection();
            // Send SMS
            Sms::send($user['phone'], $verificationCode);
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            $db->closeConnection();
            return array('verification_code_sent' => 0, 'error_msg' => 'Something went wrong.', 'error_field' => 'general', 'description' => $e->getMessage());
        }

        return array('token' => $user['access_token'], 'verification_code_sent' => 1);
    }

    private static function validateInput($post_params)
    {
        $phone = preg_replace('/[^0-9.]/', '', $post_params['phone']);
        if (preg_match('/^(359|0)[0-9]{9}$/', $phone) != 1) {
            throw new Exception('Invalid Bulgarian phone number!');
            return array('error_msg' => 'Invalid Bulgarian phone number!', 'error_field' => 'phone', 'verification_code_sent' => 0);
        }

        if (preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $post_params['email']) != 1) {
            throw new Exception('Invalid email!');
            return array('error_msg' => 'Invalid email!', 'error_field' => 'email', 'verification_code_sent' => 0);
        }

        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $post_params['password']) != 1) {
            throw new Exception('Invalid password!');
            return array('error_msg' => 'Invalid password!', 'error_field' => 'password', 'verification_code_sent' => 0);
        }

        return array($phone, $post_params['email'], $post_params['password']);
    }
}
