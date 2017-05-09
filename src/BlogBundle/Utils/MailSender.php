<?php

namespace BlogBundle\Utils;

class MailSender
{

    private $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail($sendTo, $sendFrom, $content, $type="text/html")
    {
        $message= new \Swift_Message();
        $message->setTo($sendTo);
        $message->addFrom($sendFrom);
        $message->setBody($content);


        $this->mailer->send($message);
    }
}