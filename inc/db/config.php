<?php
// config.php - Store this outside of the web root if possible

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Blank password or your actual password
define('DB_NAME', 'cms_db');

// Protect against direct access
defined('DB_SERVER') or die('Direct access is not allowed.');
