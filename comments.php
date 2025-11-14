<?php include 'connections/my_site_db.php';

$error_message = '';
$note_id = 0;
$note = null;
$comments = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note_id = (int)$_POST['comments_note_id'];
} else {
    $note_id = (int)$_GET['note'];
}

if ($note_id === 0) {
    die("Помилка: ID запису не вказано");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $created = date("Y-m-d");
    $author = trim($_POST['author']);
    $comment = trim($_POST['comment']);

    if (!empty($author) && !empty($comment)) {
        $sql = "INSERT INTO comments (created, author, comment, art_id) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssi", $created, $author, $comment, $note_id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header("Location: comments.php?note=" . $note_id);
                exit;
            } else {
                $error_message = "Помилка: " . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Помилка підготовки запиту: " . mysqli_error($link);
        }
    } else {
        $error_message = "Вкажіть автора та напишіть коментар";
    }
}

$query_note = "SELECT title, created, article FROM notes WHERE id = ?";
if ($stmt_note = mysqli_prepare($link, $query_note)) {
    mysqli_stmt_bind_param($stmt_note, "i", $note_id);
    if (mysqli_stmt_execute($stmt_note)) {
        $result = mysqli_stmt_get_result($stmt_note);
        $note = mysqli_fetch_array($result);
    }
    mysqli_stmt_close($stmt_note);
}

if (!$note) {
    die("Помилка: Запис з ID $note_id не знайдено.");
}

$query_comments = "SELECT id, author, comment, created FROM comments WHERE art_id = ? ORDER BY created DESC";
if ($stmt_comments = mysqli_prepare($link, $query_comments)) {
    mysqli_stmt_bind_param($stmt_comments, "i", $note_id);
    if (mysqli_stmt_execute($stmt_comments)) {
        $result_comments = mysqli_stmt_get_result($stmt_comments);
        while ($comment = mysqli_fetch_array($result_comments)) {
            $comments[] = $comment;
        }
    }
    mysqli_stmt_close($stmt_comments);
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Коментарі</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=delete,edit_square" />
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
        <?php
        echo "<article class='post-card-comment'>";
        echo "<h2 class='post-title'>" . htmlspecialchars($note['title']) . "</h2>";
        echo "<p class='post-meta'>Опубліковано: {$note['created']}</p>";
        echo "<div class='post-article'>" . nl2br(htmlspecialchars($note['article'])) . "</div>";
        echo "</article>";
        ?>

        <a href="editnote.php?note=<?php echo $note_id?>" class="edit-note-link" style="margin-top: 1px">Редагувати</a>
        <a href="deletenote.php?note=<?php echo $note_id?>" class="edit-note-link" style="margin-top: 1px">Видалити</a>

        <div class="comments-block">
            <h3 class="comments-header">Коментарі:</h3>
            <?php
            // Виведення коментарів
            if (empty($comments)) {
                echo "<p>Цей запис ще ніхто не коментував.</p>";
            } else {
                foreach ($comments as $comment) {
                    echo "<div class='comment-item'>";
                    echo "<a href='editcomment.php?comment={$comment['id']}' class='comment-edit-link' title='Редагувати коментар'>";
                    echo "  <span class='material-symbols-outlined'>edit_square</span>";
                    echo "</a>";
                    echo "<a href='deletecomment.php?comment={$comment['id']}' class='comment-delete-link' title='Видалити коментар'>";
                    echo "  <span class='material-symbols-outlined'>delete</span>";
                    echo "</a>";
                    echo "<p class='comment-author'>" . htmlspecialchars($comment['author']) . "</p>";
                    echo "<p class='comment-meta'>{$comment['created']}</p>";
                    echo "<p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        <div class="post-card-comment" style="margin-top: 24px;">
            <h3 class="comments-header">Додати свій коментар</h3>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="margin-top: 15px;"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="comments.php" method="POST" class="new-note-form" name="newcomment" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="author">Ваше ім'я:</label>
                    <input type="text" id="author" name="author" maxlength="50" required>
                </div>

                <div class="form-group">
                    <label for="comment">Коментар:</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>
                </div>

                <input type="hidden" name="comments_note_id" value="<?php echo $note_id; ?>">
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