<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Success</title>
</head>
<body>
    <h1>Your message has been sent</h1>
    <button onclick="window.location.href='index.html';">Click here to go back to search </button>
</body>
</html>
<?php
require_once '../../vendor/autoload.php';

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$transport = Transport::fromDSN('smtp://3d41ff0f469575:e7f2a53af15dfb@sandbox.smtp.mailtrap.io:2525');

$mailer = new Mailer($transport);

$email = (new Email());

$email->from('CS490D5@outlook.com');

$email->to(
  'pilan003@csusm.edu'
);

$email->subject('Collaborator Test');

$email->text('The plain text message');

$email->html('
<h1 style="color: #fff300; background-color: #0073ff; width 500px; padding: 16px 0; text-align:center border-radius: 50px;">
The HTML version of the message.
</h1>
');

$mailer->send($email);

