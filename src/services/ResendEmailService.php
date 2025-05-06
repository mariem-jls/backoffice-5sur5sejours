<?php

namespace App\Service;

use Resend;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Dotenv\Dotenv;
use GuzzleHttp\Exception\RequestException;

class ResendEmailService
{
    private Client $client;
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(
        string $apiKey,
        LoggerInterface $logger,
    ) {
        // $dotenv = new Dotenv();
        // $dotenv->load(__DIR__ . '/../.env');

        $this->client = new Client();
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function sendEmail(string $to, string $subject, string $body): void
    {
        $resend = Resend::client('re_3ijtfF8W_AQyRsePq3jmmAaEohQig9Se8');
        try {
            $resend->emails->send([
                'from' => 'Acme <onboarding@resend.dev>',
                'to' => ['mohamedyaakoubiweb@gmail.com'],
                'subject' => $subject,
                'text' => $body,
                'headers' => [
                    'X-Entity-Ref-ID' => '123456789',
                ],
                'tags' => [
                    [
                        'name' => 'category',
                        'value' => 'confirm_email',
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email via Resend: ' . $e->getMessage());
        }
    }
}
