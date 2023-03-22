<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
require '../vendor/autoload.php';

//session starten
session_start([
    "cookie_lifetime" => 86400,
]);


$json = file_get_contents('php://input');
$values = json_decode($json, true);

//php session vorhand ?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($values['name']) && isset($values['emailInput']) && isset($values['alter']) && isset($values['bewerbungsText'])) {

    $date = new DateTime('NOW');
    $date->modify('+7 day');
    $dateformat =  $date->format("d.m.Y");

    $datebewerbung = new DateTime('NOW');
    $datebewerbung->modify('+9 day');
    $datebewerbungdc = $datebewerbung->format("d.m.Y") . " zwischen 20:00 Uhr und 0:00 Uhr";

    $name = $values['name'];
    $emailadresse = $values['emailInput'];
    $alter = $values['alter'];
    $bewerbungsText = $values['bewerbungsText'];

    $mailme = new PHPMailer(true);
    $mailother = new PHPMailer(true);

    try {
        //send email to me
        //1. Verbindungsaufbau SMTP-Server (gmail, gmx ...)
        /* SMTP parameters. */
        $mailme->isSMTP();
        $mailme->Host = 'smtp.gmail.com';
        $mailme->SMTPAuth = true;
        $mailme->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailme->Port = 465;
        $mailme->Username = 'dcsupp00t@gmail.com';
        $mailme->Password = 'wemswaxnxqvlainv';
        $mailme->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mailme -> isHTML();


        //2. from = lani-webseite@gmail.com
        $mailme->setFrom("dcsupp00t@gmail.com", "Real-Life-Team Bewerbungen");
        //3. to = lani.schlagheck@gmail.com
        $mailme->addAddress('dcsupp00t@gmail.com', 'Real-Life-Team Bewerbungen');

        //4. message = Erledigt|°
        $mailme->Subject = "Bewerbung von {$name}";
        $mailme->Body = "Hallo liebes Real Life Team Hier ein paar  eck daten von mir:<br> <b>Name</b>: {$name}
        <br><b>Meine E-Mail Adresse</b>: {$emailadresse}<br>
        <b>Alter</b>: {$alter}<br> <b>Mein Bewerbungs Text</b>: {$bewerbungsText}";

        //send mail other
        //1. Verbindungsaufbau SMTP-Server (gmail, gmx ...)
        /* SMTP parameters. */
        $mailother->isSMTP();
        $mailother->Host = 'smtp.gmail.com';
        $mailother->SMTPAuth = true;
        $mailother->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailother->Port = 465;
        $mailother->Username = 'dcsupp00t@gmail.com';
        $mailother->Password = 'wemswaxnxqvlainv';
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
        $mailother->addAddress("{$emailadresse}", "{$name}");

        //4. message = Erledigt|°
        $mailother->Subject = "Bewerbung von {$name} Erhalten";
        $mailother->Body = "Sehr geerte/r  {$name}<br>Wir haben ihre Bewerbung erhaltern mit folgenden Daten:<br> <b>Name</b>: {$name}
        <br><b>E-Mail Adresse</b>: {$emailadresse}<br>
        <b>Alter</b>: {$alter}<br> <b>Mein Bewerbungs Text</b>: {$bewerbungsText}.<br><br> Wir haben deine Bewerbung erhalten und werden diese nun bearbeiten.<br>
        Sollten wir uns bis zum {$dateformat}, nicht melden, komm bitte am<br>{$datebewerbungdc} auf unseren Discord https://rlteam.eu/dc f&uuml;r ein<br>Bewerbungsgespr&auml;ch.<br>";


        
        //Senden der E-Mail an mich
        if(!$mailme->send()) {
            echo json_encode($mailme->ErrorInfo);
            http_response_code(502);
            return;
        }

        //Senden der E-Mail an Anderen
        if(!$mailother->send()) {
            echo json_encode($mailother->ErrorInfo);
            http_response_code(502);
            return;
        }
        
        echo json_encode("E-Mail wurde versendet");
        http_response_code(200);

    } catch (Exception $e) {
        echo json_encode($e->getMessage());
        http_response_code(501);
    } catch (\Exception $e) {
        echo json_encode($e->getMessage());
        http_response_code(500);
    }
} else {
    echo json_encode("Die Methode ist nicht erlaubt");
    http_response_code(405);
}
?>