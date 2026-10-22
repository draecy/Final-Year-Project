<?php
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email            = trim($_POST['email']);
        $full_name        = trim($_POST['full_name']);
        $password         = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        // Build URL query parameters to keep form values (excluding passwords)
        $old_inputs = http_build_query([
            'email'     => $email,
            'full_name' => $full_name
        ]);
        //$allowedDomain = '@staff.psis.edu.my';

        if (empty($email) || empty($full_name) || empty($password) || empty($confirm_password)) {
            header("Location: admin_register.php?error=emptyfields&" . $old_inputs);
            exit();
        }

        if ($password !== $confirm_password) {
            header("Location: admin_register.php?error=passwordmismatch&" . $old_inputs);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: admin_register.php?error=invalidemail&" . $old_inputs);
            exit();
        }

        /*if (strtolower(substr($email, -strlen($allowedDomain))) !== $allowedDomain) {
            alert("Registration/Login restricted to @staff.psis.edu.my email addresses only.")
            header("Location: admin_register.php");
            exit();
        }*/

        try {
            $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);

            $stmt = $pdo->prepare("SELECT id FROM admin WHERE email = ?");
            $stmt->execute([$sanitized_email]);

            if ($stmt->fetch()) {
                header("Location: admin_register.php?error=userexists&" . $old_inputs);
                exit();
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO admin (email, full_name, password, role) VALUES (?, ?, ?, 'admin')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sanitized_email, $full_name, $hashed_password]);

            header("Location: admin_login.php?signup=success");
            exit();

        } catch (PDOException $e) {
            header("Location: admin_register.php?error=systemerror&" . $old_inputs);
            exit();
        }
    } else {
        header("Location: admin_register.php");
        exit();
    }
?>