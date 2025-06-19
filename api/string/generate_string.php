<?php
header('Content-Type: application/json');

$length = intval($_POST['length'] ?? 0);
$charset = $_POST['charset'] ?? 'letters';

if ($length <= 0 || $length > 1000) {
    echo json_encode(['error' => 'Lungimea este invalidă.']);
    exit;
}

switch ($charset) {
    case 'letters':
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        break;
    case 'letters_digits':
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        break;
    case 'digits':
        $chars = '0123456789';
        break;
    case 'all':
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}';
        break;
    default:
        echo json_encode(['error' => 'Tip necunoscut.']);
        exit;
}

$shuffled = '';
$maxIndex = strlen($chars) - 1;
for ($i = 0; $i < $length; $i++) {
    $shuffled .= $chars[rand(0, $maxIndex)];
}

echo json_encode(['text' => $shuffled]);
