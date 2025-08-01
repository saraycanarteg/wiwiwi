<?php
// complementos/mail.php

function sendEmail($to, $subject, $message, $headers = '') {
    // Validate email address
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // Set default headers if none provided
    if (empty($headers)) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@caterbid.com" . "\r\n";
    }

    // Send email
    return mail($to, $subject, $message, $headers);
}

function sendConfirmationEmail($userEmail, $userName) {
    $subject = "Confirmación de Registro";
    $message = "<html><body>";
    $message .= "<h1>Hola, $userName!</h1>";
    $message .= "<p>Gracias por registrarte en Caterbid. Estamos emocionados de tenerte con nosotros.</p>";
    $message .= "</body></html>";

    return sendEmail($userEmail, $subject, $message);
}

function sendQuoteEmail($clientEmail, $quoteDetails) {
    $subject = "Detalles de tu Cotización";
    $message = "<html><body>";
    $message .= "<h1>Detalles de Cotización</h1>";
    $message .= "<p>Aquí están los detalles de tu cotización:</p>";
    $message .= "<p>$quoteDetails</p>";
    $message .= "</body></html>";

    return sendEmail($clientEmail, $subject, $message);
}
?>