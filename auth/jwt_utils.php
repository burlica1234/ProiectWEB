<?php
// auth/jwt_utils.php

require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const SECRET_KEY = '!Simbakun01';
const JWT_ALG = 'HS256';

/**
 * Generează un token JWT valabil 1 oră.
 */
function generate_jwt(array $payload): string {
    $payload['iat'] = time();               // issued at
    $payload['exp'] = time() + 3600;        // expires in 1 hour
    return JWT::encode($payload, SECRET_KEY, JWT_ALG);
}

/**
 * Verifică validitatea unui token JWT.
 * Returnează payload-ul dacă e valid, altfel false.
 */
function verify_jwt(string $token) {
    try {
        return JWT::decode($token, new Key(SECRET_KEY, JWT_ALG));
    } catch (Exception $e) {
        return false;
    }
}
