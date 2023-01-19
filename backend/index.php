<?php

header("Access-Control-Allow-Origin: http://localhost:3000");

require_once(__DIR__ . '/app/libs/Router');
require_once(__DIR__ . '/app/libs/Request');
require_once(__DIR__ . '/app/libs/Response');
require_once(__DIR__ . "/app/services/Signup.php");
require_once(__DIR__ . "/app/services/VerificationCode.php");

Router::post('/signup', function (Request $req, Response $res) {    
    $res->toJSON(Signup::addUser($req->getBody()));
});

Router::post('/verify_user', function (Request $req, Response $res) {
    $res->toJSON(VerificationCode::verifyCode($req->getBody()));
});

Router::post('/resend_verification_code', function (Request $req, Response $res) {
    $res->toJSON(VerificationCode::resendCode($req->getBody()));
});
