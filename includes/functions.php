<?php
// functions.php - Shared utility functions

function uploadImage($file) {
    $targetDir = "../uploads/";
    $timestamp = time();
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $uniqueName = $timestamp . "_" . uniqid() . "." . $extension;
    $targetFile = $targetDir . $uniqueName;

    $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
    if (in_array(mime_content_type($file["tmp_name"]), $allowedTypes) && $file["size"] < 5000000) { // 5MB limit
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $uniqueName;
        }
    }
    return false;
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>