<?php
/*
 * Project:     Beacon
 * File:        MailService.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Services;

use App\Exceptions\MailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class MailService
{
    public function __construct(
        private PHPMailer       $mailer,
        private LoggerInterface $logger,
    ) { }

    /**
     * @throws MailerException
     */
    public function send(
        string|array $to,
        string $subject,
        string $body,
        bool $isHtml = false
    ): bool {
        $address = $to;
        $name = '';

        if (is_array($to)) {
            if (!array_key_exists('email', $to)) {
                throw new MailerException('Missing email address.');
            }

            $address = $to['email'];
            $name = $to['name'] ?? '';
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($address, $name);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML($isHtml);

            if ($this->mailer->send() === false) {
                throw new MailerException($this->mailer->ErrorInfo);
            }
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            throw new MailerException($e->getMessage());
        }

        return true;
    }
}