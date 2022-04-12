<?
header('Location: https://' . $_SERVER['HTTP_HOST'] . '/' . strtok($_SERVER["REQUEST_URI"], '/') . '/goldmanstacks/view/signin.php'); // Redirect to sign in page