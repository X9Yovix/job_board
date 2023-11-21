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

    public function send(string $to, string $subject, string $template, array $context)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@job-board.tn'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("mails/$template")
            ->context($context);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw $exception->getMessage();
        }
    }
}
