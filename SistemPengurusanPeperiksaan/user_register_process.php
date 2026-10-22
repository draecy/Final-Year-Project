<?php
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email            = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $full_name        = trim($_POST['full_name'] ?? '');
        $password         = trim($_POST['password'] ?? ''); 
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        $old_inputs = http_build_query([
            'email'     => $email,
            'full_name' => $full_name
        ]);

        if (empty($email) || empty($full_name) || empty($password) || empty($confirm_password)) {
            header("Location: user_register.php?error=emptyfields&" . $old_inputs);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: user_register.php?error=invalidemail&" . $old_inputs);
            exit();
        }

        if ($password !== $confirm_password) {
            header("Location: user_register.php?error=passwordmismatch&" . $old_inputs);
            exit();
        }

        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                header("Location: user_register.php?error=userexists&" . $old_inputs);
                exit();
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new student user
            $stmt = $pdo->prepare("INSERT INTO users (email, full_name, password, role, reset_password) VALUES (?, ?, ?, 'student', NULL)");
            $stmt->execute([$email, $full_name, $hashed_password]);

            header("Location: index.php?signup=success");
            exit();

        } catch (PDOException $e) {
            header("Location: user_register.php?error=systemerror&" . $old_inputs);
            exit();
        }
    } else {
        header("Location: user_register.php");
        exit();
    }
?>