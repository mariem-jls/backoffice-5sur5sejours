<?php

declare(strict_types=1);
namespace App\Message;
final class SendEmail
{
    private string $recipient;
    private string $subject;
    private string $twig;
    private array $extraData;
    public function __construct(string $recipient, string $subject,  string $twig, array $extraData)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->twig = $twig;
        $this->extraData = $extraData;
    }
    public function getRecipient(): string
    {
        return $this->recipient;
    }
    public function getSubject(): string
    {
        return $this->subject;
    }
    public function getTwig(): string
    {
        return $this->twig;
    }
    public function getExtraData(): array
    {
        return $this->extraData;
    }
}