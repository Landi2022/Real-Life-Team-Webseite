<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';

//session starten
session_start([
    "cookie_lifetime" => 86400,
]);

//application/json
/*$json = file_get_contents('php://input');
$values = json_decode($json, true);
*/

//multipart/form-data

//php session vorhand ?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailInput']) && isset($_POST['autor']) && isset($_POST['modname']) && isset($_POST['bewerbungsText']) && isset($_FILES["file"])) {

    $date = new DateTime('NOW');
    $date->modify('+7 day');
    $dateformat =  $date->format("d.m.Y");

    $datebewerbung = new DateTime('NOW');
    $datebewerbung->modify('+9 day');
    $datebewerbungdc = $datebewerbung->format("d.m.Y") . " zwischen 20:00 Uhr und 0:00 Uhr";

    $email = $_POST['emailInput'];
    $autor = $_POST['autor'];
    $modname = $_POST['modname'];
    $bewerbungsText = $_POST['bewerbungsText'];
    $file = $_FILES['file'];

    $mailme = new PHPMailer(true);
    $mailother = new PHPMailer(true);

    //Fileupload
    $filePath = "./../uploads/".$_FILES["file"]["name"];
    if(!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
        //TODO error
        http_response_code(405);
        return;
    }
    

    try {
        //send email to me
        //1. Verbindungsaufbau SMTP-Server (gmail, gmx ...)
        /* SMTP parameters. */
        $mailme->isSMTP();
        $mailme->Host = 'cmail01.mailhost24.de';
        $mailme->SMTPAuth = true;
        $mailme->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailme->Port = 465;
        $mailme->Username = 'noreply.bewerbung@real-life-team.de';
        $mailme->Password = 'RLT?Bewerbung!AutoAntwort2022';
        $mailme->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mailme->isHTML();


        //2. from = lani-webseite@gmail.com
        $mailme->setFrom("noreply.bewerbung@real-life-team.de", "Real-Life-Team Bewerbungen");
        //3. to = lani.schlagheck@gmail.com
        $mailme->addAddress('noreply.bewerbung@real-life-team.de', 'Real-Life-Team Bewerbungen');

        //4. message = Erledigt|°
        $mailme->Subject = "Mod anfrage von {$autor}";
        $mailme->Body = "Hallo liebes Real Life Team Hier ein paar Eckdaten von dem Mod:<br> <b>Name</b>: {$modname}
        <br><b>Meine E-Mail Adresse</b>: {$email}<br>
        <b>Autoren</b>: {$autor}<br> <b>Mod Name</b>: {$modname}<br> <b>Mod Beschreibung</b>: {$bewerbungsText}";

        $mailme->addAttachment($filePath);


        //send mail other
        //1. Verbindungsaufbau SMTP-Server (gmail, gmx ...)
        /* SMTP parameters. */
        $mailother->isSMTP();
        $mailother->Host = 'cmail01.mailhost24.de';
        $mailother->SMTPAuth = true;
        $mailother->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailother->Port = 465;
        $mailother->Username = 'noreply.bewerbung@real-life-team.de';
        $mailother->Password = 'RLT?Bewerbung!AutoAntwort2022';
        $mailother->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mailother -> isHTML();


        //2. from = lani-webseite@gmail.com
        $mailother->setFrom("noreply.bewerbung@real-life-team.de", "Real-Life-Team Bewerbungen");
        //3. to = lani.schlagheck@gmail.com
        $mailother->addAddress("{$email}", "{$autor}");

        //4. message = Erledigt|°
        $mailother->Subject = "Mod Upload von {$email} Erhalten";
        $mailother->Body = "Sehr geerte/r  {$autor}<br>Wir haben ihre Mod Upload anfrage erhaltern mit folgenden Daten:<br><b>Name</b>: {$modname}
        <br><b>Meine E-Mail Adresse</b>: {$email}<br>
        <b>Autoren</b>: {$autor}<br> <b>Mod Name</b>: {$modname}<br> <b>Mod Beschreibung</b>: {$bewerbungsText}.<br> Wir haben deine Mod Upload Anfrage erhalten und werden diese nun bearbeiten.<br>";
        
        $mailother->addAttachment($filePath);

        //Senden der E-Mail an mich
        if(!$mailme->send()) {
            echo json_encode($mailme->ErrorInfo);
            http_response_code(502);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return;
        }

        //Senden der E-Mail an Anderen
        if(!$mailother->send()) {
            echo json_encode($mailother->ErrorInfo);
            http_response_code(502);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return;
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        echo json_encode("E-Mail wurde versendet");
        http_response_code(200);

    } catch (Exception $e) {
        echo json_encode($e->getMessage());
        http_response_code(501);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    } catch (\Exception $e) {
        echo json_encode($e->getMessage());
        http_response_code(500);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
} else {
    echo json_encode("Die Methode ist nicht erlaubt");
    http_response_code(405);
}
?>