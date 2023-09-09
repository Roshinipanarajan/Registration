<?php

session_start();
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginEmail = $_POST["login-email"];
    $loginPassword = $_POST["login-password"];

    $redis = new Redis();
    $redis->connect("127.0.0.1", 6379);
    $cacheKey = "user:$loginEmail";
    $cachedUser = $redis->get($cacheKey);

    if ($cachedUser !== false) {
        $user = json_decode($cachedUser, true);
        $hashedPassword = $user["password"];

        if (verifyPassword($loginPassword, $hashedPassword)) {
            $_SESSION["user"] = $user;
            header("Location: profile.php");
            exit();
        } else {
            echo json_encode(["message" => "Invalid login credentials"]);
        }
    } else {
        echo json_encode(["message" => "User not found"]);
    }
} else {
    echo json_encode(["message" => "Invalid request method"]);
}

function hashPassword($password) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}
?>
