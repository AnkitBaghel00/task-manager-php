<?php
session_start();
require_once "includes/db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}


$user_id = $_SESSION["user_id"];

$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? AND (title LIKE ? OR status LIKE ?) ORDER BY deadline");
$stmt->bind_param("iss", $user_id, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Welcome, <?php echo $_SESSION["username"]; ?>!</h3>
    <a href="add_task.php" class="btn btn-success mb-3">+ Add Task</a>
       
    <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
    <form method="GET" class="mb-3 d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Search by title or status..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <button type="submit" class="btn btn-outline-primary">Search</button>
</form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["title"]); ?></td>
                <td><?php echo htmlspecialchars($row["description"]); ?></td>
                <td><?php echo $row["deadline"]; ?></td>
                <td><?php echo $row["status"]; ?></td>
                <td>
                    <a href="edit_task.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_task.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
 
</div>
</body>
</html>
