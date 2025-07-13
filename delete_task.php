<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET["id"])) {
    $task_id = $_GET["id"];
    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
}

header("Location: dashboard.php");
exit();
