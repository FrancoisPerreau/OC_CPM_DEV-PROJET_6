<?php
// src/CitrespBundle/Services/Mailer.php

namespace CitrespBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use CitrespBundle\Entity\User;
use CitrespBundle\Entity\Reporting;

class Mailer
{
    protected $mailer;
    protected $templating;

    protected $from ;
    

    // protected $template = '@CitrespBundle/Emails/notifivation.html.twig';
    

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, $adminMail)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->from = $adminMail;
    }

    protected function sendMessage($to, $subject, $body)
    {
        $mail = new \Swift_Message();

        $mail
            ->setFrom($this->from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setContentType('text/html')
        ;

        $this->mailer->send($mail);
    }


    /** 
     * Envoie d'un e-mail quand un signalement a été commenté
     */
    public function sendNewCommentMessage(User $user, Reporting $reporting)
    {
        $to = $user->getEmail();
        $subject = 'Un de vos signalements à été commenté';

        $template = '@Citresp/Emails/notificationNewComment.html.twig';
        
        $body = $this->templating->render($template, [
            'user' => $user,
            'reporting' => $reporting
        ]);

        $this->sendMessage($to, $subject, $body);
    }


    /** 
     * Envoie d'un e-mail quand un signalement a changé de statut
     */
    public function sendNewReportingStatus(User $user, Reporting $reporting)
    {
        $to = $user->getEmail();
        $subject = 'Un de vos signalements a changé de statut';

        $template = '@Citresp/Emails/notificationNewStatus.html.twig';
        
        $body = $this->templating->render($template, [
            'user' => $user,
            'reporting' => $reporting
        ]);

        $this->sendMessage($to, $subject, $body);
    }


    /** 
     * Envoie d'un e-mail quand un signalement a été modéré
     */
    public function sendNewReportingModerate(User $user, Reporting $reporting)
    {
        $to = $user->getEmail();
        $subject = 'Un de vos signalements a changé été modéré';

        $template = '@Citresp/Emails/notificationNewModerateReporting.html.twig';
        
        $body = $this->templating->render($template, [
            'user' => $user,
            'reporting' => $reporting
        ]);

        $this->sendMessage($to, $subject, $body);
    }


    /** 
     * Envoie d'un e-mail quand un commentaire a été modéré
     */
    public function sendNewCommentModerate($comment, User $user, Reporting $reporting)
    {
        $to = $user->getEmail();
        $subject = 'Un de vos signalements a changé été modéré';

        $template = '@Citresp/Emails/notificationNewModerateComment.html.twig';
        
        $body = $this->templating->render($template, [
            'comment' => $comment,
            'user' => $user,
            'reporting' => $reporting
        ]);

        $this->sendMessage($to, $subject, $body);
    }



}
