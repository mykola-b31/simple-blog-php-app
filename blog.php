<?php include 'connections/my_site_db.php'; ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мій Блог</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="newnote.php" class="menu_link">Новий запис</a></li>
                <li class="menu_item"><a href="email.php" class="menu_link">Надіслати повідомлення</a></li>
                <li class="menu_item"><a href="#" class="menu_link">Фото</a></li>
                <li class="menu_item"><a href="#" class="menu_link">Файли</a></li>
                <li class="menu_item"><a href="#" class="menu_link">Адміністратору</a></li>
                <li class="menu_item"><a href="inform.html" class="menu_link">Інформація</a></li>
                <li class="menu_item"><a href="#" class="menu_link">Увійти</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="welcome-message">
            <h2>Вітаю вас у своєму блозі!</h2>
            <p>На сторінках цього сайту я буду ділитися нотатками та записами зі своїх подорожей, а також іншими цікавими матеріалами!</p>
        </div>

        <?php
        $sql = "SELECT * FROM notes ORDER BY created DESC;";
        $select_note = mysqli_query($link, $sql);
        while ($note = mysqli_fetch_array($select_note)) {
            echo "<article class='post-card'>";
            echo "<h3 class='post-title'><a href='comments.php?note={$note['id']}'>{$note['title']}</a></h3>";
            echo "<p class='post-meta'>Опубліковано: {$note['created']}</p>";
            echo "<div class='post-article'>{$note['article']}</div>";
            echo "</article>";
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>