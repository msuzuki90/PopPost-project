<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendContactEmail(string $to, string $subject, string $body, string $fromName): void
    {
        $email = (new Email())
            ->from(new Address('contact@paic-france.com', $fromName))
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->html('<p>' . nl2br($body) . '</p>');

        $this->mailer->send($email);
    }
}
