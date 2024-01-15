<?php
if (($_SERVER['REMOTE_ADDR'] == '127.0.0.1' or $_SERVER['REMOTE_ADDR'] == '::1')) {

} else {
    require 'vendor/autoload.php';
}

/*
    Fichier : /Helpers/functions.php
 */

/**
 * Retourne le contenu HTML du bloc d'en tête d'une page.
 * Deux CSS sont automatiquement intégré :
 *   - pico.css
 *   - custom.css
 *
 * @param string title le titre de la page.
 * @return string
 */
function head(string $title = ''): string
{
    return  <<<HTML_HEAD
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="icon" type="image/png" href="./assets/img/659fabf161f3c_FOUEE2.png" />
  <title>$title</title>
</head>

HTML_HEAD;
}


/**
 * Retourne vrai si la méthode d'appel est GET.
 */
function isGetMethod(): bool
{
    return ($_SERVER['REQUEST_METHOD'] === 'GET');
}
/**
 * Envoyer un mail de confirmation.
 */
const APP_URL = 'https://app.mmi-companion.fr/pages';
const SENDER_EMAIL_ADDRESS = 'raphael.tiphonet@etu.univ-poitiers.fr';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_activation_email(string $email, string $activation_code,)
{
    global $dbh;
    $infosQuery = "SELECT * FROM settings";
    $infosResult = $dbh->query($infosQuery);
    $infos = $infosResult->fetch();
    global $infos;
    // set email subjectj
    $subject = 'Activez votre compte dès maintenant !';
    $message = <<<HTML
    <h1>Confirmation de votre inscription</h1>
    <p>Merci de cliquer sur le lien suivant pour confirmer votre inscription :</p>
    <a href="https://rtiphonet.fr/foodtruck/confirmMail.php?token=$activation_code&email=$email">Confirmer mon inscription</a>
HTML;

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'From:' . $infos['nom_entreprise'] . '<' . SENDER_EMAIL_ADDRESS . '>' . "\r\n" .
        'Reply-To: ' . SENDER_EMAIL_ADDRESS . "\r\n" .
        'Content-Type: text/html; charset="utf-8"' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    // send the email
    $_SESSION['mail_message'] = "";
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = "mail.rtiphonet.fr";                    // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = "foodtruck@rtiphonet.fr";                     // SMTP username
        $mail->Password   = "y9AtkG7Z]oG7";                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = "465";                 // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";

        //Recipients
        $mail->setFrom(SENDER_EMAIL_ADDRESS, 'MMI Companion');
        $mail->addAddress($email);     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        $_SESSION['mail_message'] = "Le mail vient de t'être envoyé, penses à regarder dans tes spams si besoin.";
    } catch (Exception $e) {
        $_SESSION['mail_message'] = "Une erreur vient de survenir lors de l'envoi du mail, réessaye plus tard.";
        error_log("Error sending activation email to $email");
    }
}
/**
 * Retourne vrai si la méthode d'appel est POST.
 */
function isPostMethod(): bool
{
    return ($_SERVER['REQUEST_METHOD'] === 'POST');
}
/**
 * Affiche le footer du HTML.
 *
 * @return void
 */
function footer(): string
{
    return  <<<HTML_FOOTER
<footer></footer>
</body>
</html>
HTML_FOOTER;
}
