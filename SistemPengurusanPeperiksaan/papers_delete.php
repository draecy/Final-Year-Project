<?php 
    require_once 'config.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
        header("Location: admin_login.php");
        exit();
    }

    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $paper_id = $_GET['id'];

        try {
            $fetch_sql = "SELECT pdf_file FROM past_papers WHERE paper_id = ?";
            $fetch_stmt = $pdo->prepare($fetch_sql);
            $fetch_stmt->execute([$paper_id]);
            $paper = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

            if($paper) {
                $file_path = "uploads/papers/".$paper['pdf_file'];

                if(file_exists($file_path)) {
                    unlink($file_path);
                }

                $delete_sql = "DELETE FROM past_papers WHERE paper_id = ?";
                $delete_stmt = $pdo->prepare($delete_sql);
                $delete_stmt->execute([$paper_id]);

                echo "<script>alert('Past paper deleted successfully!'); window.location='papers_manage.php';</script>";
                exit();
            } else {
                echo "<script>alert('Paper not found!'); window.location='papers_manage.php';</script>";
                exit();
            }
        } catch (PDOException $e) {
            die("Database Error: ".$e->getMessage());
        }
    } else {
        header("Location: papers_manage.php");
        exit();
    }
?>