<?php include 'connections/my_site_db.php';
$success_message = '';
$error_message = '';

$note_id = 0;
if (isset($_GET['note'])) {
    $note_id = (int)$_GET['note'];
}

if ($note_id === 0) {
    die ("Помилка: ID запису не вказано.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $article = $_POST['article'];

    if (!empty($title) && !empty($article)) {
        $sql_update = "UPDATE notes SET title = ?, article = ? WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql_update)) {
            mysqli_stmt_bind_param($stmt, "ssi", $title, $article, $note_id);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Запис успішно змінено!";
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
}

$sql_select = "SELECT * FROM notes WHERE id = ?";
$edit_note = null;

if ($stmt_select = mysqli_prepare($link, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "i", $note_id);

    if (mysqli_stmt_execute($stmt_select)) {
        $result = mysqli_stmt_get_result($stmt_select);
        $edit_note = mysqli_fetch_array($result);

        if (!$edit_note) {
            die ("Помилка: Запис з ID $note_id не знайдено.");
        }
    }
    mysqli_stmt_close($stmt_select);
} else {
    die ("Критична помилка запиту до БД.");
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагування запису</title>
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
            <h2>Редагувати нотатку</h2>
            <p>Внесіть зміни та натисніть "Зберегти"</p>

            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="editnote.php?note=<?php echo $note_id; ?>" method="POST" class="new-note-form" name="editnote">
                <div class="form-group">
                    <label for="title">Заголовок замітки:</label>
                    <input type="text" id="title" name="title" maxlength="50" value="<?php echo htmlspecialchars($edit_note['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="article">Текст повідомлення:</label>
                    <textarea id="article" name="article" rows="10" required><?php echo htmlspecialchars($edit_note['article']); ?></textarea>
                </div>
                <button type="submit" name="submit" class="send-button">Зберегти зміни</button>
            </form>
            <a href="comments.php?note=<?php echo $note_id?>" class="back-to-notes-link" style="margin-top: 20px;">Повернутися до замітки</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>


