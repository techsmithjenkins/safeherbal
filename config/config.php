<?php
// config.php - General site configuration
if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/fredyherbal'); // Update with your domain
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../uploads/');
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../');
}
if (!defined('DEBUG')) {
    define('DEBUG', true); // Set to false in production
}
?>