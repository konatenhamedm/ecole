<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{

    /*
     *@var MailerInterface
     */
    private $mailer;

    /*
    *@var Environment
    */
    private $twig;

    public function __construct(MailerInterface  $mailer,Environment $twig)
    {
        $this->mailer=$mailer;
        $this->twig=$twig;
    }

    public  function  send(string $subject,string $from,string $to,string $template,array $parameters):void
    {
        $email=(new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html(
                $this->twig->render($template,$parameters)
            );
        $this->mailer->send($email);
    }

}