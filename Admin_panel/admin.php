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

    $sql = "SELECT id, email, ad, soyad FROM users WHERE tckn = ? AND email = ?";
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
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    display: flex;
    width: 80%;
    max-width: 1200px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

.left {
    flex: 1;
    background-color: #f0f2f5;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.left img.logo {
    max-width: 100%;
    height: auto;
    margin-bottom: 20px;
}

.left p {
    margin-bottom: 10px;
    line-height: 1.5;
    text-align: center;
}

.right {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.right h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

label {
    display: none; /* Gizledik çünkü placeholder kullanacağız */
}

input[type="text"],
input[type="email"],
input[type="password"] {
    margin-bottom: 15px;
    padding: 15px;
    width: 100%;
    max-width: 400px;
    border: 1px solid #ccc;
    border-radius: 25px; /* Kenarları yuvarladık */
    font-size: 16px;
    box-sizing: border-box;
}

input[type="submit"] {
    padding: 15px;
    width: 100%;
    max-width: 400px;
    background-color: #4e73df;
    color: #fff;
    border: none;
    border-radius: 25px; /* Kenarları yuvarladık */
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 16px;
    box-sizing: border-box;
}

input[type="submit"]:hover {
    background-color: #4e73df;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="1_1 (1).jpg" alt="Logo" class="logo">
        </div>
        <div class="right">
            <h2>İYİLİK MASASI</h2>
            <form action="admin.php" method="post">
                <input type="text" name="tckn" id="tckn" placeholder="TCKN" required><br>
                <input type="email" name="email" id="email" placeholder="Email" required><br>
                <input type="submit" value="Giriş">
            </form>
        </div>
    </div>
</body>
</html>



