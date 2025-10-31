<?php
include 'connections/my_site_db.php';

$comment_id = 0;
$note_id = 0;
$comment_author = null;
$comment_text = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['comment_id_to_delete']) && isset($_POST['note_id_to_return'])) {
        $comment_id = (int)$_POST['comment_id_to_delete'];
        $note_id = (int)$_POST['note_id_to_return'];

        if ($comment_id > 0) {
            $sql_delete = "DELETE FROM comments WHERE id = ?";
            if ($stmt_delete = mysqli_prepare($link, $sql_delete)) {
                mysqli_stmt_bind_param($stmt_delete, "i", $comment_id);
                mysqli_stmt_execute($stmt_delete);
                mysqli_stmt_close($stmt_delete);
            }
        }
    }

    mysqli_close($link);
    header("Location: comments.php?note=" . $note_id);
    exit;
}

if (isset($_GET['comment'])) {
    $comment_id = (int)$_GET['comment'];
}

if ($comment_id === 0) {
    die("Помилка: ID коментаря не вказано.");
}

$sql_select = "SELECT author, art_id, comment FROM comments WHERE id = ?";
if ($stmt_select = mysqli_prepare($link, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "i", $comment_id);
    if (mysqli_stmt_execute($stmt_select)) {
        $result = mysqli_stmt_get_result($stmt_select);
        $comment = mysqli_fetch_array($result);
        if ($comment) {
            $comment_author = $comment['author'];
            $note_id = $comment['art_id'];
            $comment_text = $comment['comment'];
        } else {
            die("Помилка: Коментар з ID $comment_id не знайдено.");
        }
    }
    mysqli_stmt_close($stmt_select);
}
mysqli_close($link);

if ($comment_author === null) {
    die("Не вдалося отримати дані про коментар.");
}

$snippet = mb_substr($comment_text, 0, 70);
if (mb_strlen($comment_text) > 70) {
    $snippet .= "...";
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Підтвердження видалення</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Підтвердження видалення</h2>
            <p>Ви впевнені, що хочете назавжди видалити цей коментар?</p>
            <div style="margin: 15px 0; padding: 10px; background-color: #2c3e50; border-radius: 5px; border-left: 4px solid #00ADB5;">
                <p style="margin: 0; font-weight: bold; color: #EEEEEE;">Автор: <?php echo htmlspecialchars($comment_author); ?></p>
                <p style="margin: 5px 0 0 0; font-style: italic; color: #bdc3c7;">
                    "<?php echo htmlspecialchars($snippet); ?>"
                </p>
            </div>

            <p style="text-align: center; color: #c0392b;">Видалений коментар буде неможливо відновити.</p>

            <form action="deletecomment.php" method="POST" style="display: inline-block; margin-right: 15px;">
                <input type="hidden" name="comment_id_to_delete" value="<?php echo $comment_id; ?>">
                <input type="hidden" name="note_id_to_return" value="<?php echo $note_id; ?>">
                <button type="submit" class="send-button" style="background-color: #c0392b;">Так, видалити</button>
            </form>

            <a href="comments.php?note=<?php echo $note_id; ?>" class="back-to-notes-link">Скасувати</a>
        </div>
    </div>
</main>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>