<?php
require_once('../classes/database.php');
$con = new database();

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $result = $con->checkEmailExists($email); // You need to implement this in your class

    echo json_encode(['exists' => $result]);
}
?>