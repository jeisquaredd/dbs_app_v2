<?php

require_once('classes/database.php');
$con = new database();

if (isset($_POST['add_course'])){

    $coursename = $_POST['course_name'];

    $con->savecourse($coursename);


}

