<?php


require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const SECRET_KEY = '!Simbakun01';
const JWT_ALG = 'HS256';


function generate_jwt(array $payload): string {
    $payload['iat'] = time();               
    $payload['exp'] = time() + 3600;        
    return JWT::encode($payload, SECRET_KEY, JWT_ALG);
}


function verify_jwt(string $token) {
    try {
        return JWT::decode($token, new Key(SECRET_KEY, JWT_ALG));
    } catch (Exception $e) {
        return false;
    }
}
