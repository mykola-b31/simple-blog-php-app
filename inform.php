<?php session_start(); ?>
<?php include 'connections/my_site_db.php';

function get_scalar_query($link, $sql) {
    $result = mysqli_query($link, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        return $row[0];
    }
    return 0;
}

$total_notes = get_scalar_query($link, "SELECT COUNT(id) FROM notes");
$total_comments = get_scalar_query($link, "SELECT COUNT(id) FROM comments");
$notes_last_month = get_scalar_query($link, "SELECT COUNT(id) FROM notes WHERE created >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$comments_last_month = get_scalar_query($link, "SELECT COUNT(id) FROM comments WHERE created >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");

$last_note_link = "Немає записів";
$result_last_note = mysqli_query($link, "SELECT id, title FROM notes ORDER by created DESC LIMIT 1");
if ($result_last_note && mysqli_num_rows($result_last_note) > 0) {
    $last_note = mysqli_fetch_array($result_last_note);
    $last_note_link = "<a href='comments.php?note={$last_note['id']}'>" . htmlspecialchars($last_note['title']) . "</a>";
}

$most_discussed_link = "Коментарів ще немає";
$sql_most_discussed = "SELECT n.id, n.title, COUNT(c.id) AS comment_count
                       FROM comments AS c
                       JOIN notes AS n ON c.art_id = n.id
                       GROUP BY c.art_id
                       ORDER BY comment_count DESC
                       LIMIT 1";
$result_most_discussed = mysqli_query($link, $sql_most_discussed);
if ($result_most_discussed && mysqli_num_rows($result_most_discussed) > 0) {
    $most_discussed = mysqli_fetch_array($result_most_discussed);
    $most_discussed_link = "<a href='comments.php?note={$most_discussed['id']}'>" . htmlspecialchars($most_discussed['title']) . "</a> (коментарів: {$most_discussed['comment_count']})";
}

$top_commentator = "Ще немає";
$sql_top_commentator = "SELECT author, COUNT(id) AS cnt FROM comments GROUP BY author ORDER BY cnt DESC LIMIT 1";
$result_top_commentator = mysqli_query($link, $sql_top_commentator);
if ($result_top_commentator && mysqli_num_rows($result_top_commentator) > 0) {
    $row_top = mysqli_fetch_array($result_top_commentator);
    $top_commentator = htmlspecialchars($row_top['author']) . " (" . $row_top['cnt'] . " коментарів)";
}

$avg_length = get_scalar_query($link, "SELECT AVG(CHAR_LENGTH(article)) FROM notes");
$avg_length = round($avg_length);

$longest_note_link = "Немає записів";
$sql_longest = "SELECT id, title, CHAR_LENGTH(article) AS char_len FROM notes ORDER BY char_len DESC LIMIT 1";
$result_longest = mysqli_query($link, $sql_longest);
if ($result_longest && mysqli_num_rows($result_longest) > 0) {
    $longest = mysqli_fetch_array($result_longest);
    $longest_note_link = "<a href='comments.php?note={$longest['id']}'>" . htmlspecialchars($longest['title']) . "</a> ({$longest['char_len']} символів)";
}

$first_post_date = get_scalar_query($link, "SELECT MIN(created) FROM notes");
if (!$first_post_date) {
    $first_post_date = "Ще не настав";
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Інформація</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
                <li class="menu_item"><a href="#" class="menu_link">Новий запис</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="info-card">
            <h2>Корисна інформація</h2>
            <p>Зроблено записів - <strong><?php echo $total_notes; ?></strong></p>
            <p>Залишено коментарів - <strong><?php echo $total_comments; ?></strong></p>
            <p>За останній місяць я створив записів - <strong><?php echo $notes_last_month; ?></strong></p>
            <p>За останній місяць залишено коментарів - <strong><?php echo $comments_last_month; ?></strong></p>
            <p>Мій останній запис - <?php echo $last_note_link; ?></p>
            <p>Найбільш обговорюваний запис - <?php echo $most_discussed_link; ?></p>
            <hr style="border: 1px solid #00ADB5; margin: 15px 0;">
            <p>Найактивніший коментатор - <strong><?php echo $top_commentator; ?></strong></p>
            <p>Середня довжина моїх нотаток - <strong><?php echo $avg_length; ?> символів</strong></p>
            <p>Найдовша стаття - <?php echo $longest_note_link; ?></p>
            <p>День народження блогу (перший пост) - <strong><?php echo $first_post_date; ?></strong></p>
            <br>
            <a href="blog.php" class="back-to-notes-link">На головну сторінку сайту</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>