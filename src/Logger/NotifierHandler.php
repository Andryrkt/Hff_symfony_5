<?php

namespace App\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotifierHandler extends AbstractProcessingHandler
{
    private $notifier;
    private $adminEmail;

    public function __construct(NotifierInterface $notifier, string $adminEmail, $level = Logger::ERROR, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->notifier = $notifier;
        $this->adminEmail = $adminEmail;
    }

    protected function write(array $record): void
    {
        $notification = new Notification($record['message'], ['email']);
        $recipient = new Recipient($this->adminEmail);
        $this->notifier->send($notification, $recipient);
    }
}
