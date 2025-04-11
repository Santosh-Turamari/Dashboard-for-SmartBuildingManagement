<?php
session_start();
include 'db.php';

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        header("Location: index.php");
        exit();
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $password);
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;

            // Initialize default room (Living Room)
            $room_name = "Living Room";
            $temperature = 25;
            $humidity = 8;
            $stmt = $conn->prepare("INSERT INTO rooms (user_id, name, temperature, humidity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $user_id, $room_name, $temperature, $humidity);
            $stmt->execute();
            $room_id = $conn->insert_id;

            // Initialize default devices
            $devices = [
                ['name' => 'Air Conditioner', 'type' => 'switch', 'status' => 'On'],
                ['name' => 'Television', 'type' => 'switch', 'status' => 'On'],
                ['name' => 'Door Lock', 'type' => 'lock', 'status' => 'UNLOCKED'],
                ['name' => 'Curtain', 'type' => 'curtain', 'status' => 'CLOSED'],
                ['name' => 'Windows', 'type' => 'window', 'status' => 'Closed']
            ];
            $stmt = $conn->prepare("INSERT INTO devices (room_id, name, type, status) VALUES (?, ?, ?, ?)");
            foreach ($devices as $device) {
                $stmt->bind_param("isss", $room_id, $device['name'], $device['type'], $device['status']);
                $stmt->execute();
            }

            // Initialize default media settings
            $station = "JAZZ VIBES";
            $volume = 60;
            $playing = 1;
            $bluetooth = 0;
            $stmt = $conn->prepare("INSERT INTO media_settings (user_id, station, volume, playing, bluetooth_connected) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isiii", $user_id, $station, $volume, $playing, $bluetooth);
            $stmt->execute();

            $_SESSION['success'] = "Successfully registered! You can now login.";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
            header("Location: index.php");
            exit();
        }
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password. Please try again.";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: index.php");
        exit();
    }
}
?>