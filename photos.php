<?php
$upload_dir = 'uploads/photos/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$message = '';
$message_type = '';

// завантаження
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (in_array($mime_type, $allowed_types)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('img_', true) . '.' . $ext;

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                $message = "Фото успішно завантажено!";
                $message_type = 'success-message';
            } else {
                $message = "Помилка при збереженні файлу.";
                $message_type = 'error-message';
            }
        } else {
            $message = "Дозволені тільки зображення (JPG, PNG, GIF, WEBP).";
            $message_type = 'error-message';
        }
    } else {
        $message = "Помилка завантаження: код " . $file['error'];
        $message_type = 'error-message';
    }
}

// видалення
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_file'])) {
    $file_to_delete = basename($_POST['delete_file']);
    $file_path = $upload_dir . $file_to_delete;

    if (file_exists($file_path) && is_file($file_path)) {
        unlink($file_path);
        $message = "Файл видалено.";
        $message_type = 'success-message';
    }
}

$files = glob($upload_dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Фотогалерея</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=delete" />
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
                <li class="menu_item"><a href="files.php" class="menu_link">Файли</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Фотогалерея</h2>
            <p>Завантажуйте та переглядайте фотографії з ваших подорожей.</p>

            <?php if (!empty($message)): ?>
                <div class="<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="upload-area">
                <form action="photos.php" method="POST" enctype="multipart/form-data">
                    <label for="photo" style="margin-right: 10px; color: #bdc3c7;">Оберіть фото:</label>
                    <input type="file" name="photo" id="photo" required style="color: #EEEEEE;">
                    <button type="submit" class="send-button">Завантажити</button>
                </form>
            </div>

            <div class="gallery-grid">
                <?php if (empty($files)): ?>
                    <p style="color: #bdc3c7; grid-column: 1/-1; text-align: center;">Фотографій поки немає.</p>
                <?php else: ?>
                    <?php foreach ($files as $filepath):
                        $filename = basename($filepath);
                        ?>
                        <div class="gallery-item">
                            <a href="<?php echo $filepath; ?>" target="_blank">
                                <img src="<?php echo $filepath; ?>" alt="Photo" class="gallery-img">
                            </a>
                            <div class="gallery-actions">
                                <span class="file-name" title="<?php echo $filename; ?>"><?php echo $filename; ?></span>
                                <form action="photos.php" method="POST" onsubmit="return confirm('Видалити це фото?');" style="margin:0;">
                                    <input type="hidden" name="delete_file" value="<?php echo $filename; ?>">
                                    <button type="submit" class="icon-btn" title="Видалити">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>