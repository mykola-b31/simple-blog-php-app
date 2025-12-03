<?php
session_start();
if (!isset($_SESSION['user_rights']) || $_SESSION['user_rights'] !== 'a') {
    header("Location: login.php");
    exit;
}

include 'connections/my_site_db.php';

$note_id = 0;
$note_title = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['note_id_to_delete'])) {
        $note_id = (int)$_POST['note_id_to_delete'];
    }

    if ($note_id > 0) {

        mysqli_begin_transaction($link);

        try {
            $sql_comments = "DELETE FROM comments WHERE art_id = ?";
            $stmt_comments = mysqli_prepare($link, $sql_comments);
            mysqli_stmt_bind_param($stmt_comments, "i", $note_id);
            mysqli_stmt_execute($stmt_comments);
            mysqli_stmt_close($stmt_comments);

            $sql_note = "DELETE FROM notes WHERE id = ?";
            $stmt_note = mysqli_prepare($link, $sql_note);
            mysqli_stmt_bind_param($stmt_note, "i", $note_id);
            mysqli_stmt_execute($stmt_note);
            mysqli_stmt_close($stmt_note);

            mysqli_commit($link);

        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($link);
            error_log("Помилка видалення (транзакція): " . $exception->getMessage());
        }
    }

    mysqli_close($link);
    header("Location: blog.php");
    exit;
}

if (isset($_GET['note'])) {
    $note_id = (int)$_GET['note'];
}

if ($note_id === 0) {
    die("Помилка: ID запису не вказано.");
}

$sql_select = "SELECT title FROM notes WHERE id = ?";
if ($stmt_select = mysqli_prepare($link, $sql_select)) {
    mysqli_stmt_bind_param($stmt_select, "i", $note_id);
    if (mysqli_stmt_execute($stmt_select)) {
        $result = mysqli_stmt_get_result($stmt_select);
        $note = mysqli_fetch_array($result);
        if ($note) {
            $note_title = $note['title'];
        } else {
            die("Помилка: Запис з ID $note_id не знайдено.");
        }
    }
    mysqli_stmt_close($stmt_select);
}
mysqli_close($link);

if ($note_title === null) {
    die("Не вдалося отримати дані про запис.");
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
            <p>Ви впевнені, що хочете назавжди видалити запис:</p>
            <h3 style="text-align: center;">"<?php echo htmlspecialchars($note_title); ?>"</h3>
            <p style="text-align: center; color: #c0392b;">Видалену замітку буде неможливо відновити.</p>

            <form action="deletenote.php" method="POST" style="display: inline-block; margin-right: 15px;">
                <input type="hidden" name="note_id_to_delete" value="<?php echo $note_id; ?>">
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