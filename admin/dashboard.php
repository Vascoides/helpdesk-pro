<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Garantir que é admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

// Buscar todos os tickets
$stmt = $pdo->query("
    SELECT tickets.*, users.name 
    FROM tickets 
    JOIN users ON tickets.user_id = users.id
    ORDER BY tickets.created_at DESC
");
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard | Helpdesk Pro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>

    <p>
        Logged as <strong><?php echo $_SESSION['user_name']; ?></strong> |
        <a href="../auth/logout.php">Logout</a>
    </p>

    <h3>All Tickets</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
        </tr>

        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?php echo $ticket['id']; ?></td>
                <td><?php echo htmlspecialchars($ticket['name']); ?></td>
                <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                <td><?php echo $ticket['status']; ?></td>
                <td><?php echo $ticket['created_at']; ?></td>
                <td>
                    <a href="ticket_view.php?id=<?php echo $ticket['id']; ?>">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
