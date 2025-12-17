<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

$error = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);

    if (empty($subject) || empty($description)) {
        $error = "All fields are required.";
    } else {
        $sql = "INSERT INTO tickets (user_id, subject, description) VALUES (:user_id, :subject, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':subject' => $subject,
            ':description' => $description
        ]);
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Ticket | Helpdesk Pro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<h2>Create New Ticket</h2>

<?php if ($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="subject" placeholder="Subject"><br><br>
    <textarea name="description" placeholder="Description" rows="5" cols="40"></textarea><br><br>
    <button type="submit">Submit Ticket</button>
</form>

<p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
