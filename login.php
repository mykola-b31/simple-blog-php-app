<?php
session_start();
include 'connections/my_site_db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    if (!empty($name) && !empty($password)) {
        $sql = "SELECT * FROM privileges WHERE name = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_rights'] = $user['rights'];
                header("Location: blog.php");
                exit;
            } else {
                $error = "Невірний логін або пароль.";
            }
        }
    } else {
        $error = "Заповніть всі поля.";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Вхід у систему</h2>
            <?php if($error) echo "<p class='error-message'>$error</p>"; ?>
            <form method="POST" action="login.php" class="new-note-form">
                <div class="form-group">
                    <label>Логін:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="send-button">Увійти</button><br>
                <a href="blog.php" class="back-to-notes-link" style="margin-top: 20px;">Повернутися на головну</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>