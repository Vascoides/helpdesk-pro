<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Apenas admin
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$ticket_id = $_GET['id'];
$admin_id = $_SESSION['user_id'];
$success = "";

// Buscar ticket
$stmt = $pdo->prepare("
    SELECT tickets.*, users.name 
    FROM tickets 
    JOIN users ON tickets.user_id = users.id 
    WHERE tickets.id = :id
");
$stmt->execute([':id' => $ticket_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket not found.");
}

// Buscar respostas
$stmt = $pdo->prepare("
    SELECT responses.*, users.name 
    FROM responses 
    JOIN users ON responses.admin_id = users.id
    WHERE responses.ticket_id = :id
    ORDER BY responses.created_at ASC
");
$stmt->execute([':id' => $ticket_id]);
$responses = $stmt->fetchAll();

// Nova resposta
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $message = trim($_POST['message']);
    $status = $_POST['status'];

    if (!empty($message)) {
        $stmt = $pdo->prepare("
            INSERT INTO responses (ticket_id, admin_id, message)
            VALUES (:ticket_id, :admin_id, :message)
        ");
        $stmt->execute([
            ':ticket_id' => $ticket_id,
            ':admin_id' => $admin_id,
            ':message' => $message
        ]);

        $stmt = $pdo->prepare("
            UPDATE tickets SET status = :status WHERE id = :id
        ");
        $stmt->execute([
            ':status' => $status,
            ':id' => $ticket_id
        ]);

        $success = "Response sent successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ticket #<?php echo $ticket_id; ?> | Helpdesk Pro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Ticket #<?php echo $ticket['id']; ?></h2>

    <p><strong>User:</strong> <?php echo htmlspecialchars($ticket['name']); ?></p>
    <p><strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
    <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
    <p><strong>Status:</strong> <?php echo $ticket['status']; ?></p>

    <hr>

    <h3>Responses</h3>

    <?php if ($responses): ?>
        <?php foreach ($responses as $response): ?>
            <p>
                <strong><?php echo $response['name']; ?>:</strong><br>
                <?php echo nl2br(htmlspecialchars($response['message'])); ?><br>
                <small><?php echo $response['created_at']; ?></small>
            </p>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No responses yet.</p>
    <?php endif; ?>

    <h3>Add Response</h3>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST">
        <textarea name="message" rows="4" placeholder="Write your response..."></textarea>

        <select name="status">
            <option value="Aberto">Aberto</option>
            <option value="Em progresso">Em progresso</option>
            <option value="Resolvido">Resolvido</option>
        </select>

        <button type="submit" onclick="return confirm('Send response?')">
            Send Response
        </button>
    </form>

    <p><a href="dashboard.php">⬅ Back to Admin Dashboard</a></p>
</div>

</body>
</html>
