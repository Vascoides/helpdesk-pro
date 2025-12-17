<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Garantir que é utilizador normal
if ($_SESSION['user_role'] !== 'user') {
    header("Location: ../admin/dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar tickets do utilizador
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = :id ORDER BY created_at DESC");
$stmt->execute([':id' => $user_id]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard | Helpdesk Pro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>

    <p>
        <a href="create_ticket.php">➕ Create Ticket</a> |
        <a href="../auth/logout.php">Logout</a>
    </p>

    <h3>Your Tickets</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>

        <?php if (count($tickets) > 0): ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?php echo $ticket['id']; ?></td>
                    <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                    <td><?php echo $ticket['status']; ?></td>
                    <td><?php echo $ticket['created_at']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No tickets created yet.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
