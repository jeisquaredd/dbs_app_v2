<?php
require_once('../classes/database.php');
$con = new database();

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    // Check if the username exists
    if ($con->isUsernameExists($username)) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
