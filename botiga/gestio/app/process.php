<?php
session_start();
// Check role: admin or treballador
if (empty($_SESSION['user']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'treballador')) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['file'] ?? '';
    if (!$file) {
        header('Location: comandes.php');
        exit;
    }

    $pendingDir = __DIR__ . '/../../comandes_copia';
    $processedDir = __DIR__ . '/../comandes_gestionades';
    $source = $pendingDir . '/' . $file;
    $dest = $processedDir . '/' . $file;

    if (isset($_POST['process'])) {
        if (file_exists($source)) {
            if (!is_dir($processedDir)) mkdir($processedDir, 0777, true);
            if (rename($source, $dest)) {
                echo "<script>alert('Comanda processada correctament.'); window.location.href='comandes.php';</script>";
            } else {
                echo "Error al moure l'arxiu.";
            }
        } else {
            echo "L'arxiu no existeix.";
        }
    } elseif (isset($_POST['email'])) {
        // Mock email
        echo "<script>alert('Correu enviat al client (simulació).'); window.location.href='detall_comanda.php?file=".urlencode($file)."';</script>";
    }
} else {
    header('Location: comandes.php');
}
?>