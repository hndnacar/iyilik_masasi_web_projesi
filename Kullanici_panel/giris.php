<?php
session_start();
include 'baglan.php';   

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\PHPMailer/PHPMailer/src/Exception.php';
require 'C:\xampp\htdocs\PHPMailer/PHPMailer/src/PHPMailer.php';
require 'C:\xampp\htdocs\PHPMailer/PHPMailer/src/SMTP.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tckn = htmlspecialchars(trim($_POST['tckn']));
    $email = htmlspecialchars(trim($_POST['email']));

    $sql = "SELECT id, email, firstname, lastname FROM users2 WHERE tckn = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $tckn, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($kullanici_id, $userEmail, $firstname, $lastname);
        $stmt->fetch();

        // 6 haneli doğrulama kodu oluştur
        $verificationCode = rand(100000, 999999);

        // Doğrulama kodunu oturuma kaydet
        $_SESSION['verification_code'] = $verificationCode;
        $_SESSION['kullanici_id'] = $kullanici_id;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;

        // PHPMailer kullanarak doğrulama kodunu e-posta ile gönder
        $mail = new PHPMailer(true);

        try {
            // SMTP ayarları
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP sunucusu
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Gmail hesabınızın e-posta adresi
            $mail->Password = ''; // Gmail hesabınızın şifresi
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // E-posta ayarları
            $mail->setFrom('your_email@gmail.com', 'Yenişehir Bursa Belediye');
            $mail->addAddress($userEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Doğrulama Kodu';
            $mail->Body    = "Giriş yapmak için doğrulama kodunuz: $verificationCode";

            $mail->send();
            header("Location: dogrulama.php");
            exit();
        } catch (Exception $e) {
            echo "E-posta gönderimi başarısız. Hata: {$mail->ErrorInfo}";
        }
    } else {
        echo "TC Kimlik Numarası veya E-posta yanlış.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="1_1 (1).jpg" alt="Logo" class="logo">
        </div>
        <div class="right">
            <h2>İYİLİK MASASI</h2>
            <form action="giris.php" method="post">
                <input type="text" name="tckn" id="tckn" placeholder="TCKN" required><br>
                <input type="email" name="email" id="email" placeholder="Email" required><br>
                <input type="submit" value="Giriş">
            </form>
            <p>Hesabınız yok mu? <a href="kayit.php">Hesap Oluştur</a></p>
        </div>
    </div>
</body>
</html>
