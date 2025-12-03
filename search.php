<?php session_start(); ?>
<?php
include 'connections/my_site_db.php';

$search_query = '';
$results = [];
$error_message = '';
$search_words = [];

function highlightKeywords($text, $words) {
    $text = htmlspecialchars($text);

    if (empty($words) || trim($text) === '') {
        return $text;
    }

    foreach ($words as $word) {
        $quoted_word = preg_quote($word, '/');

        $text = preg_replace(
            '/(' . $quoted_word . ')/iu',
            '<mark class="highlight">$0</mark>',
            $text
        );
    }
    return $text;
}

if (isset($_GET['query'])) {
    $raw_query = trim($_GET['query']);

    $clean_query = str_replace(',', ' ', $raw_query);
    $clean_query = preg_replace('/\s+/', ' ', $clean_query);

    $search_query = $clean_query;

    if (!empty($search_query)) {
        $search_words = explode(' ', $search_query);
        $search_words = array_filter($search_words);

        if (count($search_words) > 0) {
            $sql = "SELECT * FROM notes WHERE ";
            $conditions = [];
            $types = "";
            $params = [];

            foreach ($search_words as $word) {
                $conditions[] = "(title LIKE ? OR article LIKE ?)";
                $types .= "ss";
                $params[] = "%" . $word . "%";
                $params[] = "%" . $word . "%";
            }

            $sql .= implode(' OR ', $conditions);
            $sql .= " ORDER BY created DESC";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);

                if (mysqli_stmt_execute($stmt)) {
                    $result_set = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_array($result_set)) {
                        $results[] = $row;
                    }
                } else {
                    $error_message = "Помилка виконання пошуку.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Помилка підготовки запиту.";
            }
        }
    }
}
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результати пошуку</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=search" />
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
            <h2>Пошук по сайту</h2>
            <form action="search.php" method="GET" class="search-form">
                <div class="search-group">
                    <input type="text" name="query" placeholder="Введіть слова для пошуку..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="search-btn">
                        <span class="material-symbols-outlined">search</span>
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($search_query)): ?>
            <h3 style="color: #1e405c; margin-bottom: 20px;">
                Результати пошуку за запитом: "<?php echo htmlspecialchars($search_query); ?>"
            </h3>

            <?php if (empty($results) && empty($error_message)): ?>
                <div class="welcome-message" style="background-color: #c0392b;">
                    <p>На жаль, нічого не знайдено.</p>
                </div>
            <?php elseif (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php else: ?>
                <?php foreach ($results as $note): ?>
                    <article class='post-card'>
                        <h3 class='post-title'>
                            <a href='comments.php?note=<?php echo $note['id']; ?>'>
                                <?php
                                echo highlightKeywords($note['title'], $search_words);
                                ?>
                            </a>
                        </h3>
                        <p class='post-meta'>Опубліковано: <?php echo $note['created']; ?></p>
                        <div class='post-article'>
                            <?php
                            $plain_article = $note['article'];
                            $snippet = mb_substr($plain_article, 0, 200) . '...';
                            echo nl2br(highlightKeywords($snippet, $search_words));
                            ?>
                        </div>
                        <a href="comments.php?note=<?php echo $note['id']; ?>" class="back-to-notes-link" style="margin-top: 10px; font-size: 14px;">Читати далі</a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>