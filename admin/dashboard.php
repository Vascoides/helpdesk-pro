<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Verifica se o utilizador é admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

// Obter filtro de estado (opcional)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Query tickets
if ($statusFilter) {
    $sql = "SELECT tickets.*, users.name as user_name 
            FROM tickets 
            JOIN users ON tickets.user_id = users.id 
            WHERE status = :status
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':status' => $statusFilter]);
} else {
    $sql = "SELECT tickets.*, users.name as user_name 
            FROM tickets 
            JOIN users ON tickets.user_id = users.id
            ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
}

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard | Helpdesk Pro</title>
</head>
<body>
<h2>Admin Dashboard</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> | <a href="../auth/logout.php">Logout</a></p>

<h3>Tickets</h3>

<form method="GET">
    <label>Filter by status:</label>
    <select name="status" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="Aberto" <?php if($statusFilter=='Aberto') echo 'selected'; ?>>Aberto</option>
        <option value="Em progresso" <?php if($statusFilter=='Em progresso') echo 'selected'; ?>>Em progresso</option>
        <option value="Resolvido" <?php if($statusFilter=='Resolvido') echo 'selected'; ?>>Resolvido</option>
    </select>
</form>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    <?php if ($tickets): ?>
        <?php foreach ($tickets as $ticket): ?>
        <tr>
            <td><?php echo $ticket['id']; ?></td>
            <td><?php echo htmlspecialchars($ticket['user_name']); ?></td>
            <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
            <td><?php echo $ticket['status']; ?></td>
            <td><?php echo $ticket['created_at']; ?></td>
            <td><a href="ticket_view.php?id=<?php echo $ticket['id']; ?>">View / Respond</a></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No tickets found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
