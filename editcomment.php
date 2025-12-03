<?php session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<?php include 'connections/my_site_db.php';

$success_message = '';
$error_message = '';

$comment_id = 0;

if (isset($_GET['comment'])) {
    $comment_id = (int)$_GET['comment'];
}

if ($comment_id === 0) {
    die("Помилка: ID коментаря не вказано.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author = $_POST['author'];
    $comment_text = $_POST['comment'];

    if (!empty($author) && !empty($comment_text)) {
        $sql_update = "UPDATE comments SET author = ?, comment = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql_update)) {
            mysqli_stmt_bind_param($stmt, "ssi", $author, $comment_text, $comment_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Коментар успішно змінено!";
            } else {
                $error_message = "Помилка: " . mysqli_error($link);
            }
        } else {
            $error_message = "Помилка підготовки запиту: " . mysqli_error($link);
        }
    } else {
        $error_message = "Заповніть ім'я та текст коментаря";
    }
}

$sql_select = "SELECT * FROM comments WHERE id = ?";
$edit_comment = null;

if ($stmt_select = mysqli_prepare($link, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "i", $comment_id);
    if (mysqli_stmt_execute($stmt_select)) {
        $result = mysqli_stmt_get_result($stmt_select);
        $edit_comment = mysqli_fetch_array($result);
        if (!$edit_comment) {
            die("Помилка: Коментар з ID $comment_id не знайдено.");
        }
    }
    mysqli_stmt_close($stmt_select);
} else {
    die("Критична помилка запиту до БД.");
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагування коментаря</title>
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
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Редагувати коментар</h2>
            <p>Внесіть зміни та натисніть "Зберегти"</p>

            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="editcomment.php?comment=<?php echo $comment_id; ?>" method="POST" class="new-note-form" name="editcomment">
                <div class="form-group">
                    <label for="author">Ваше ім'я:</label>
                    <input type="text" id="author" name="author" maxlength="100" value="<?php echo htmlspecialchars($edit_comment['author']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="comment">Текст коментаря:</label>
                    <textarea id="comment" name="comment" rows="5" required><?php echo htmlspecialchars($edit_comment['comment']); ?></textarea>
                </div>
                <button type="submit" name="submit" class="send-button">Зберегти зміни</button>
            </form>
            <a href="comments.php?note=<?php echo $edit_comment['art_id']; ?>" class="back-to-notes-link" style="margin-top: 20px;">Повернутися до замітки</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>
