<?php
/*
 * Project:     Beacon
 * File:        MailServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Services\MailService;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

final readonly class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            PHPMailer::class,
            function () {
                $mailer = new PHPMailer(true);
                $mailer->SMTPDebug = config('mailer.smtp_debug_level', 0);
                $mailer->isSMTP();
                $mailer->Host = config('mailer.host');
                $mailer->Port = config('mailer.port');
                $mailer->SMTPAuth = config('mailer.auth');
                $mailer->Username = config('mailer.username');
                $mailer->Password = config('mailer.password');
                $mailer->SMTPSecure = config('mailer.encryption');
                $mailer->setFrom(
                    config('mailer.from_address'),
                    config('mailer.from_name')
                );

                return $mailer;
            }
        );

        $this->container->singleton(
            MailService::class,
            fn () => new MailService(
                $this->container->make(PHPMailer::class),
                $this->container->make(LoggerInterface::class)
            )
        );
    }
}