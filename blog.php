<?php
session_start(); // 1. Стартуємо сесію
include 'connections/my_site_db.php';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мій Блог</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=search" />
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="newnote.php" class="menu_link">Новий запис</a></li>
                <li class="menu_item"><a href="email.php" class="menu_link">Надіслати повідомлення</a></li>
                <li class="menu_item"><a href="photos.php" class="menu_link">Фото</a></li>
                <li class="menu_item"><a href="files.php" class="menu_link">Файли</a></li>

                <?php if (isset($_SESSION['user_rights']) && $_SESSION['user_rights'] === 'a'): ?>
                    <li class="menu_item"><a href="users.php" class="menu_link">Адміністратору</a></li>
                <?php endif; ?>

                <li class="menu_item"><a href="inform.php" class="menu_link">Інформація</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="menu_item"><a href="logout.php" class="menu_link">Вийти (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                <?php else: ?>
                    <li class="menu_item"><a href="login.php" class="menu_link">Увійти</a></li>
                <?php endif; ?>
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

        <div class="post-card-comment" style="margin-bottom: 24px; padding: 10px 20px;">
            <form action="search.php" method="GET" class="search-form">
                <div class="search-group">
                    <input type="text" name="query" placeholder="Пошук нотаток..." required>
                    <button type="submit" class="search-btn">
                        Пошук <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">search</span>
                    </button>
                </div>
            </form>
        </div>

        <?php
        $limit = 2;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql_count = "SELECT COUNT(id) FROM notes";
        $result_count = mysqli_query($link, $sql_count);
        $row_count = mysqli_fetch_array($result_count);
        $total_records = $row_count[0];
        $total_pages = ceil($total_records / $limit);

        $sql = "SELECT * FROM notes ORDER BY created DESC LIMIT $offset, $limit";
        $select_note = mysqli_query($link, $sql);
        while ($note = mysqli_fetch_array($select_note)) {
            echo "<article class='post-card'>";
            echo "<h3 class='post-title'><a href='comments.php?note={$note['id']}'>{$note['title']}</a></h3>";
            echo "<p class='post-meta'>Опубліковано: {$note['created']}</p>";
            echo "<div class='post-article'>{$note['article']}</div>";
            echo "</article>";
        }
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $page) ? 'active' : '';
            echo "<a href='blog.php?page=$i' class='pagination-link $active_class'>$i</a>";
        }
        echo "</div>";
        ?>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>