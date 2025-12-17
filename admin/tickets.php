<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

// Apenas admins
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header("Location: dashboard.php");
    exit;
}

// Buscar ticket
$sql = "SELECT tickets.*, users.name as user_name FROM tickets JOIN users ON tickets.user_id = users.id WHERE tickets.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Ticket not found";
    exit;
}

// Buscar respostas
$sql = "SELECT responses.*, users.name as admin_name FROM responses JOIN users ON responses.admin_id = users.id WHERE ticket_id = :ticket_id ORDER BY created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':ticket_id' => $ticket_id]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enviar resposta
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $new_status = $_POST['status'];

    if (empty($message)) {
        $error = "Message cannot be empty.";
    } else {
        $sql = "INSERT INTO responses (ticket_id, admin_id, message) VALUES (:ticket_id, :admin_id, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticket_id,
            ':admin_id'  => $_SESSION['user_id'],
            ':message'   => $message
        ]);

        // Atualizar status do ticket
        $sql = "UPDATE tickets SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $new_status,
            ':id'     => $ticket_id
        ]);

        header("Location: ticket_view.php?id=" . $ticket_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket #<?php echo $ticket['id']; ?> | Helpdesk Pro</title>
</head>
<body>
<h2>Ticket #<?php echo $ticket['id']; ?></h2>
<p><strong>User:</strong> <?php echo htmlspecialchars($ticket['user_name']); ?></p>
<p><strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
<p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
<p><strong>Status:</strong> <?php echo $ticket['status']; ?></p>

<h3>Responses:</h3>
<?php if ($responses): ?>
    <?php foreach ($responses as $resp): ?>
        <p><strong><?php echo htmlspecialchars($resp['admin_name']); ?>:</strong> <?php echo nl2br(htmlspecialchars($resp['message'])); ?> <em>(<?php echo $resp['created_at']; ?>)</em></p>
    <?php endforeach; ?>
<?php else: ?>
    <p>No responses yet.</p>
<?php endif; ?>

<h3>Add Response / Change Status:</h3>
<?php if ($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <textarea name="message" rows="5" cols="50" placeholder="Type your response"></textarea><br><br>
    <label>Status:</label>
    <select name="status">
        <option value="Aberto" <?php if($ticket['status']=='Aberto') echo 'selected'; ?>>Aberto</option>
        <option value="Em progresso" <?php if($ticket['status']=='Em progresso') echo 'selected'; ?>>Em progresso</option>
        <option value="Resolvido" <?php if($ticket['status']=='Resolvido') echo 'selected'; ?>>Resolvido</option>
    </select><br><br>
    <button type="submit">Submit Response</button>
</form>

<p><a href="dashboard.php">Back to Admin Dashboard</a></p>
</body>
</html>
