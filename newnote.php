<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connections/my_site_db.php';

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $created = $_POST['created'];
    $article = $_POST['article'];

    if (!empty($title) && !empty($article)) {
        $sql = "INSERT INTO notes (title, created, article) VALUES (?, ?, ?)";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $title, $created, $article);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Новий запис успішно створено!";
            } else {
                $error_message = "Помилка: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Помилка підготовки запиту: " . mysqli_error($link);
        }
    } else {
        $error_message = "Заповніть текст та заголовок замітки";
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новий запис</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
                <li class="menu_item"><a href="inform.php" class="menu_link">Інформація</a></li>
                <li class="menu_item"><a href="#" class="menu_link">Увійти</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Додати нову нотатку</h2>
            <p>Заповніть форму нижче, щоб опублікувати новий запис у блозі</p>

            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="newnote.php" method="POST" class="new-note-form" name="newnote">
                <div class="form-group">
                    <label for="title">Заголовок замітки:</label>
                    <input type="text" id="title" name="title" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label for="article">Текст повідомлення:</label>
                    <textarea id="article" name="article" rows="10" required></textarea>
                </div>
                <input type="hidden" name="created" id="created" value="<?php echo date("Y-m-d");?>">
                <button type="submit" name="submit" class="send-button">Опублікувати</button>
            </form>
            <a href="blog.php" class="back-to-notes-link" style="margin-top: 20px;">Повернутися до заміток</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>

