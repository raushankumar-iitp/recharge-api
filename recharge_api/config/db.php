<?php
$conn = new mysqli("localhost", "root", "", "recharge_db", 4306);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}
