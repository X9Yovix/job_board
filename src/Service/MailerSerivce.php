<?php

namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function sendEmail(string $to, string $subject, string $template, array $context): bool
    {
        try {
            $email = (new TemplatedEmail())
                ->from(new Address($_ENV['MAILER_FROM'], $_ENV['APP_NAME']))
                ->to(new Address($to))
                ->subject($subject)
                ->htmlTemplate("layout/mails/$template")
                ->context($context);

            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $exception) {
            throw $exception->getMessage();
            return false;
        }
    }
}
