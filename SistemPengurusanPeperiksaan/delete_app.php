<?php
    require_once 'config.php';

    // 2. Define Allowed Tables (SECURITY: Whitelist allowed tables to prevent SQL injection)
    $allowed_tables = [
        // 'table_name' => 'primary_key_column'
        'permohonan_pengecualian' => 'id', 
        'permohonan_transkrip' => 'id',
        'permohonan_gantian' => 'id',
        'permohonan_ctce' => 'id'
    ];

    // 3. Retrieve URL Parameters
    $id       = $_GET['id'] ?? null;
    $table    = $_GET['table'] ?? null;

    // 3. Semak jika parameter 'id' wujud dalam URL
    if ($id && $table && array_key_exists($table, $allowed_tables)) {
        $primary_key = $allowed_tables[$table];

        try {
            $sql = "DELETE FROM `$table` WHERE `$primary_key` = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR); // Gunakan PARAM_STR jika ID berbentuk string/UUID

            // Jalankan query
            if ($stmt->execute()) {
                header("Location: admin_dashboard.php?status=deleted");
                exit();
            } else {
                echo "Gagal memadam rekod.";
            }
        } catch (PDOException $e) {
            die("Ralat semasa memadam rekod: " . $e->getMessage());
        }
    } else {
        header("Location: admin_dashboard.php");
        exit();
    }
?>