<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendContactEmail(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from('contact@paic-france.com')
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->html('<p>' . nl2br($body) . '</p>');

        $this->mailer->send($email);
    }
} 

