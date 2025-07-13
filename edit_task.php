<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: dashboard.php");
    exit();
}

$task_id = $_GET["id"];
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: dashboard.php");
    exit();
}

$task = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];

    $update = $conn->prepare("UPDATE tasks SET title = ?, description = ?, deadline = ?, status = ? WHERE id = ? AND user_id = ?");
    $update->bind_param("ssssii", $title, $description, $deadline, $status, $task_id, $user_id);

    if ($update->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Update failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body class="bg-light">
<div class="container mt-4 col-md-6">
    <h3 class="mb-3">Edit Task</h3>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" required class="form-control" value="<?php echo htmlspecialchars($task['title']); ?>">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label>Deadline</label>
            <input type="date" name="deadline" class="form-control" value="<?php echo $task['deadline']; ?>">
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Pending" <?php if ($task['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Completed" <?php if ($task['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
