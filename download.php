<?php
if (isset($_GET['file'])) {
    $file_path = $_GET['file'];
    $full_path = __DIR__ . "/uploads/" . basename($file_path); // Adjust path as needed
    
    if (file_exists($full_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($full_path));
        flush();
        readfile($full_path);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "No file specified.";
}
?>
