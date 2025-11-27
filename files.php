<?php
$upload_dir = 'uploads/files/';
$allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar'];
$message = '';
$message_type = '';

// завантаження
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['user_file'])) {
    $file = $_FILES['user_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_extensions)) {
            $clean_name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($file['name']));
            $new_name = time() . '_' . $clean_name;

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                $message = "Файл успішно завантажено!";
                $message_type = 'success-message';
            } else {
                $message = "Помилка збереження.";
                $message_type = 'error-message';
            }
        } else {
            $message = "Цей тип файлів заборонено.";
            $message_type = 'error-message';
        }
    } else {
        $message = "Помилка: код " . $file['error'];
        $message_type = 'error-message';
    }
}

// видалення
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_file'])) {
    $file_to_delete = basename($_POST['delete_file']);
    $file_path = $upload_dir . $file_to_delete;

    if (file_exists($file_path)) {
        unlink($file_path);
        $message = "Файл видалено.";
        $message_type = 'success-message';
    }
}

// --- ОТРИМАННЯ СПИСКУ ---
// scandir повертає список файлів і папок. Прибираємо . і ..
$all_files = array_diff(scandir($upload_dir), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Файловий архів</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=delete,download" />
</head>
<body>
<header>
    <div id="header_container">
        <a href="blog.php" class="logo-text">Мій Блог</a>
        <nav class="menu">
            <ul class="menu_list">
                <li class="menu_item"><a href="blog.php" class="menu_link">На головну</a></li>
                <li class="menu_item"><a href="photos.php" class="menu_link">Фото</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="main_content">
        <div class="post-card-comment">
            <h2>Файловий архів</h2>
            <p>Корисні документи та матеріали для завантаження.</p>

            <?php if (!empty($message)): ?>
                <div class="<?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="upload-area">
                <form action="files.php" method="POST" enctype="multipart/form-data">
                    <label for="user_file" style="margin-right: 10px; color: #bdc3c7;">Оберіть документ:</label>
                    <input type="file" name="user_file" id="user_file" required style="color: #EEEEEE;">
                    <button type="submit" class="send-button">Завантажити</button>
                </form>
                <p style="font-size: 12px; color: #bdc3c7; margin-top: 5px;">Дозволені формати: PDF, DOC, DOCX, ZIP, RAR, TXT</p>
            </div>

            <table class="files-list">
                <thead>
                <tr>
                    <th>Назва файлу</th>
                    <th>Розмір</th>
                    <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($all_files)): ?>
                    <tr><td colspan="3" style="text-align: center;">Файлів немає.</td></tr>
                <?php else: ?>
                    <?php foreach ($all_files as $filename):
                        $filepath = $upload_dir . $filename;
                        $filesize = round(filesize($filepath) / 1024, 2) . ' KB';
                        ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($filename); ?>
                            </td>
                            <td><?php echo $filesize; ?></td>
                            <td style="display: flex; gap: 10px;">
                                <a href="<?php echo $filepath; ?>" download class="icon-btn download" title="Завантажити">
                                    <span class="material-symbols-outlined">download</span>
                                </a>
                                <form action="files.php" method="POST" onsubmit="return confirm('Видалити цей файл?');" style="margin:0;">
                                    <input type="hidden" name="delete_file" value="<?php echo $filename; ?>">
                                    <button type="submit" class="icon-btn" title="Видалити">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>