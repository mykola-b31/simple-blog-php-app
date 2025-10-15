<?php include 'connections/my_site_db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog</title>
</head>
<body>
<header>
    <nav>
        <a href="#">Увійти</a> |
        <a href="#">Новий запис</a> |
        <a href="#">Надіслати повідомлення</a> |
        <a href="#">Фото</a> |
        <a href="#">Файли</a> |
        <a href="#">Адміністратору</a> |
        <a href="inform.html">Інформація</a> |
        <a href="#">Вийти</a>
    </nav>
</header>
<hr>
<p>
    <em>Вітаю вас у своєму блозі!<br><br>
    На сторінках цього сайту я буду ділитися<br><br>
    нотатками та записами зі своїх подорожей,<br><br>
    а також іншини цікавими матеріалами!</em>
</p>
<hr>
<?php
$sql = "SELECT * FROM notes ORDER BY created DESC;";
$select_note = mysqli_query($link, $sql);
while ($note = mysqli_fetch_array($select_note)) {
    echo "{$note['id']} ";
    echo "<a href='comments.php?note={$note['id']}'>{$note['title']}</a><br>";
    echo "{$note['created']}<br>";
    echo "{$note['article']}<br><hr>";
}
?>
</body>
</html>