<?php
require_once "../config/db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    if (empty($email) || empty($pass)) {
        $error = "All fields are required.";
    } else {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":email" => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_role"] = $user["role"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: ../index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Helpdesk Pro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Login</h2>

<?php if ($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Password"><br><br>
    <button type="submit">Login</button>
</form>

<p>No account? <a href="register.php">Register</a></p>

</body>
</html>
