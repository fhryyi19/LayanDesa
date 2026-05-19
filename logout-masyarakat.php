<?php
/**
 * logout-masyarakat.php
 */

require_once 'auth-masyarakat.php';
logoutUser();
header('Location: login-masyarakat.php?logout=1');
exit;
