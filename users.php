<?php
session_start();
include 'connections/my_site_db.php';

if (!isset($_SESSION['user_rights']) || $_SESSION['user_rights'] !== 'a') {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM privileges";
$result = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="uk">
<head><title>Користувачі</title><link rel="stylesheet" href="styles/style.css"></head>
<body>
<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Список користувачів</h2>
            <a href="adduser.php" class="send-button">Додати користувача</a>
            <table class="files-list" style="margin-top: 20px;">
                <tr><th>ID</th><th>Логін</th><th>Права</th></tr>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['rights'] == 'a' ? 'Адмін' : 'Користувач'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
            <a href="blog.php" class="back-to-notes-link" style="margin-top: 20px;">Повернутися на головну</a>
        </div>
    </div>
</main>
</body>
</html>