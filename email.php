<?php session_start(); ?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php';

$message_sent = false;
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $message_body = trim($_POST['message']);

    if (!empty($subject) && !empty($message_body)) {
        $mail = new PHPMailer(true);

        try {
            // Налаштування сервера
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Одержувачі
            $mail->setFrom(SMTP_USERNAME, 'Мій Блог');
            $mail->addAddress(SMTP_RECIPIENT, 'Адміністратор');

            // Вміст листа
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $message_body;

            $mail->send();
            $message_sent = true;
        } catch (Exception $e) {
            error_log("Помилка відправки: {$mail->ErrorInfo}");
            $error_message = "Повідомлення не було надіслано. Спробуйте пізніше.";
        }
    } else {
        $error_message = 'Будь ласка, заповніть тему та текст повідомлення.';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Відправка пошти</title>
    <link rel="stylesheet" href="styles/style.css">
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
            <h2>Форма для відправки повідомлення</h2>
            <p>Тут ви можете надіслати повідомлення на пошту адміністратора.</p>

            <?php if ($message_sent): ?>
                <div class="success-message">Повідомлення успішно надіслано!</div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="email.php" method="POST" class="email-form">
                <div class="form-group">
                    <label for="subject">Тема повідомлення:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Текст повідомлення:</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <button type="submit" class="send-button">Відправити</button>
            </form>

            <a href="blog.php" class="back-to-notes-link" style="margin-top: 20px;">Повернутися до заміток</a>
        </div>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Мій Блог. Усі права захищені.</p>
</footer>
</body>
</html>