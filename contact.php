<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'Ocurrió un error al procesar la solicitud.');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $asunto = htmlspecialchars($_POST["asunto"], ENT_QUOTES, 'UTF-8');
    $mensaje = htmlspecialchars($_POST["mensaje"], ENT_QUOTES, 'UTF-8');

    $file = isset($_FILES['adjunto']) ? $_FILES['adjunto'] : null;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.zeptomail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = "emailapikey"; // Reemplazar con tu nombre de usuario de ZeptoMail
        $mail->Password = 'wSsVR60g+B6mXP96mmasdes4mgsDBlmlFht0jgf363L6HK2U9Mc7wUaYAA+uH/IeF2VqRmBB9r0ukUtS1jULjN8kzlhRDiiF9mqRe1U4J3x17qnvhDzCWGtelRuOLo8LxgVqnmVkF81u'; // Reemplazar con tu contraseña de ZeptoMail

        // Configurar el remitente y el destinatario principal
        $mail->setFrom('contacto@zrcarlos20.xyz', 'Nombre del Remitente');
        $mail->addAddress($email);

        // Asunto y cuerpo del mensaje HTML
        $mail->Subject = $asunto;
        $mail->isHTML(true);
        $mail->Body    = "
            <html>
            <head>
                <title>$asunto</title>
            </head>
            <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                <div style='background-color: #fff; border-radius: 10px; padding: 20px;'>
                    <h1 style='color: #333;'>$asunto</h1>
                    <p style='color: #666;'><strong>Nombre:</strong> $nombre</p>
                    <p style='color: #666;'><strong>Email:</strong> $email</p>
                    <p style='color: #666;'><strong>Mensaje:</strong><br>$mensaje</p>
                </div>
            </body>
            </html>
        ";

        // Adjuntar archivo si se proporciona
        if ($file && $file['size'] > 0) {
            $mail->addAttachment($file['tmp_name'], $file['name']);
        }

        // Intentar enviar el correo
        if ($mail->send()) {
            // Redireccionar al usuario a la página de confirmación
            header("Location: confirmacion.html");
            exit;
        } else {
            throw new Exception('Error al enviar el correo: ' . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        $response['message'] = 'Error al enviar el correo: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit;
?>
