<?php

include "../db/config.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
    $username = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'admin';
    $status = 'active';


    // if (empty($username) || empty($email) || empty($password) || empty($confirmpassword)) {
    //     header("Location: admin/add-admin.php?error=emptyfields");
    //     exit();
    // }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: add-admin.php?error=invalidEmail");
        exit();
    }

    try {
        // Check if the email already exists
        $sql = "SELECT email FROM `Users` WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            header("Location: add-admin.php?error=emailTaken");
            exit();
        }

        // Insert the new user
        $sql = "INSERT INTO `Users` (username, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        $hashPwd = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$username, $email, $hashPwd, $role, $status]);

        header("Location: add-admin.php?signin=success");
        exit();
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        header("Location: add-admin.php?error=sqlerror");
        exit();
    }
}
}

// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
</head>
<body>
    <h2>Add New Admin</h2>
    <form method="POST" action="add-admin.php">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" name="add">Add Admin</button>
    </form>
</body>
</html>
