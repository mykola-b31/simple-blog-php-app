<?php
session_start();
include 'connections/my_site_db.php';

if (!isset($_SESSION['user_rights']) || $_SESSION['user_rights'] !== 'a') {
    header("Location: login.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $rights = $_POST['rights'];

    if (!empty($name) && !empty($password)) {

        $check_sql = "SELECT id FROM privileges WHERE name = ?";
        if ($stmt_check = mysqli_prepare($link, $check_sql)) {
            mysqli_stmt_bind_param($stmt_check, "s", $name);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $error_message = "Користувач з таким логіном вже існує.";
            } else {
                $insert_sql = "INSERT INTO privileges (name, password, rights) VALUES (?, ?, ?)";
                if ($stmt_insert = mysqli_prepare($link, $insert_sql)) {
                    mysqli_stmt_bind_param($stmt_insert, "sss", $name, $password, $rights);

                    if (mysqli_stmt_execute($stmt_insert)) {
                        header("Location: users.php");
                        exit;
                    } else {
                        $error_message = "Помилка при додаванні: " . mysqli_error($link);
                    }
                    mysqli_stmt_close($stmt_insert);
                }
            }
            mysqli_stmt_close($stmt_check);
        }
    } else {
        $error_message = "Будь ласка, заповніть всі поля.";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати користувача</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
                <li class="menu_item"><a href="users.php" class="menu_link">Користувачі</a></li>
                <li class="menu_item"><a href="logout.php" class="menu_link">Вийти (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Додати нового користувача</h2>
            <p>Створіть новий обліковий запис для доступу до сайту.</p>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="adduser.php" method="POST" class="new-note-form">
                <div class="form-group">
                    <label for="name">Логін (Ім'я користувача):</label>
                    <input type="text" id="name" name="name" maxlength="20" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" maxlength="20" required>
                </div>

                <div class="form-group">
                    <label for="rights">Права доступу:</label>
                    <select id="rights" name="rights" style="width: 100%; padding: 10px; border-radius: 5px; background-color: #2c3e50; color: #EEEEEE; border: 1px solid #4a4946;">
                        <option value="u">Користувач (User)</option>
                        <option value="a">Адміністратор (Admin)</option>
                    </select>
                </div>

                <button type="submit" class="send-button">Створити користувача</button>
            </form>

            <a href="users.php" class="back-to-notes-link" style="margin-top: 20px;">Скасувати та повернутися</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>