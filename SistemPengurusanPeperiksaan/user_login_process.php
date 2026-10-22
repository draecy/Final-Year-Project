<?php
    require_once 'config.php'; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $old_input = http_build_query(['email' => $email]);

        if (empty($email) || empty($password)) {
            header("Location: user_login.php?error=emptyfields&" . $old_input);
            exit(); 
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = 'student';

                if (isset($user['role']) && $user['role'] === "student") {
                    header("Location: user_dashboard.php");
                } else {
                    header("Location: admin_dashboard.php");
                }
                exit();
            } else {
                header("Location: index.php?error=invalidcredentials&" . $old_input);
                exit();
            }

        } catch (PDOException $e) {
            header("Location: index.php?error=systemerror&" . $old_input);
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
?>