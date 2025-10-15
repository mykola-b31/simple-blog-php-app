<?php include 'connections/my_site_db.php';
$note_id = $_GET['note'];

$query = "SELECT title, created, article FROM notes WHERE id = $note_id";
$select_note = mysqli_query($link, $query);
$note = mysqli_fetch_array($select_note);
echo "<strong>{$note['title']}</strong><br>";
echo "{$note['created']}<br>";
echo "{$note['article']}<br><hr><hr>";

$query_comments = "SELECT author, comment, created FROM comments WHERE art_id = $note_id ORDER BY created DESC";
$select_comments = mysqli_query($link, $query_comments);
if (mysqli_num_rows($select_comments) == 0) {
    echo "Цей запис ще ніхто не коментував";
} else {
    while ($comment = mysqli_fetch_array($select_comments)) {
        echo "<em>{$comment['author']}</em><br>";
        echo "<small>{$comment['created']}</small><br>";
        echo "{$comment['comment']}<br><hr>";
    }
}