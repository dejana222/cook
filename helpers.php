<?php
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('redirectIfNotLoggedIn')) {
    function redirectIfNotLoggedIn() {
        if (!isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
}
?>