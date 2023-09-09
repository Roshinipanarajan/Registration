<?php
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashedPassword = hashPassword($password);
    $phone = $_POST["phone"];

    // Store data in MySQL
    $servername = "localhost";
    $mysqlUsername = "root";
    $mysqlPassword = "";
    $dbname = "registration_db";

    $conn = new mysqli($servername, $mysqlUsername, $mysqlPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO users (username, email, password, phone) VALUES ('$username', '$email', '$hashedPassword', '$phone')";

    if ($conn->query($sql) !== TRUE) {
        echo json_encode(["message" => "Registration failed: " . $conn->error]);
        $conn->close();
        exit();
    }

    $conn->close();

    // Store data in MongoDB
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $db = $mongoClient->registration_db;
    $collection = $db->users;
    $mongoDocument = [
        'name' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'phone' => $phone,
    ];
    $collection->insertOne($mongoDocument);

    // Use Redis as a cache
    $redis = new Redis();
    $redis->connect("127.0.0.1", 6379);
    $cacheKey = "user:$username";
    $cacheValue = json_encode($mongoDocument);
    $redis->set($cacheKey, $cacheValue);

    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone; 

    header("Location: profile.php");
    exit();
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
