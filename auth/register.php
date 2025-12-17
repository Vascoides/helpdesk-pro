<?php
require_once "../config/db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    if (empty($name) || empty($email) || empty($pass)) {
        $error = "All fields are required.";
    } else {
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ":name" => $name,
                ":email" => $email,
                ":password" => $hashedPassword
            ]);

            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | Helpdesk Pro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Create Account</h2>

<?php if ($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="name" placeholder="Full name"><br><br>
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Password"><br><br>
    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>

</body>
</html>
