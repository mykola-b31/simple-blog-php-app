<?php include 'connections/my_site_db.php'; ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Коментарі</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
                <li class="menu_item"><a href="inform.html" class="menu_link">Інформація</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <?php
        $note_id = $_GET['note'];

        // Виведення запису
        $query = "SELECT title, created, article FROM notes WHERE id = $note_id";
        $select_note = mysqli_query($link, $query);
        $note = mysqli_fetch_array($select_note);

        echo "<article class='post-card-comment'>";
        echo "<h2 class='post-title'>{$note['title']}</h2>";
        echo "<p class='post-meta'>Опубліковано: {$note['created']}</p>";
        echo "<div class='post-article'>{$note['article']}</div>";
        echo "</article>";
        ?>

        <a href="editnote.php?note=<?php echo $note_id?>" class="edit-note-link" style="margin-top: 1px">Редагувати</a>
        <a href="deletenote.php?note=<?php echo $note_id?>" class="edit-note-link" style="margin-top: 1px">Видалити</a>

        <div class="comments-block">
            <h3 class="comments-header">Коментарі:</h3>
            <?php
            // Виведення коментарів
            $query_comments = "SELECT author, comment, created FROM comments WHERE art_id = $note_id ORDER BY created DESC";
            $select_comments = mysqli_query($link, $query_comments);
            if (mysqli_num_rows($select_comments) == 0) {
                echo "<p>Цей запис ще ніхто не коментував.</p>";
            } else {
                while ($comment = mysqli_fetch_array($select_comments)) {
                    echo "<div class='comment-item'>";
                    echo "<p class='comment-author'>{$comment['author']}</p>";
                    echo "<p class='comment-meta'>{$comment['created']}</p>";
                    echo "<p>{$comment['comment']}</p>";
                    echo "</div>";
                }
            }
            ?>
            <a href="blog.php" class="back-to-notes-link">Повернутися до заміток</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>