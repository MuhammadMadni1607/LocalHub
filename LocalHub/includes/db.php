<?php

$conn = mysqli_connect("localhost", "root", "mz070716", "localhub");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

?>