<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Obter ID do utilizador
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Buscar tickets do utilizador
$sql = "SELECT * FROM tickets WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard | Helpdesk Pro</title>
</head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>

<p><a href="../auth/logout.php">Logout</a> | <a href="create_ticket.php">Create New Ticket</a></p>

<h3>Your Tickets:</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Created At</th>
    </tr>
    <?php if ($tickets): ?>
        <?php foreach ($tickets as $ticket): ?>
        <tr>
            <td><?php echo $ticket['id']; ?></td>
            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
            <td><?php echo $ticket['status']; ?></td>
            <td><?php echo $ticket['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">You have no tickets.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
