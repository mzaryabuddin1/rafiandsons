<?php

namespace App\Libraries;

use App\Models\SettingModel;
use Config\Services;

class VendorMailService
{
    public function sendApplicationReceived(array $vendor): void
    {
        $email = trim((string) ($vendor['email'] ?? ''));
        if ($email === '') {
            return;
        }

        $name = htmlspecialchars((string) ($vendor['contact_name'] ?? 'Vendor'));
        $business = htmlspecialchars((string) ($vendor['business_name'] ?? ''));

        $this->sendHtml(
            $email,
            'Your vendor application is under review',
            $this->wrap(
                '<h2 style="color:#c8102e;margin:0 0 12px;">Application received</h2>'
                . '<p>Dear ' . $name . ',</p>'
                . '<p>Thank you for applying to become a vendor' . ($business !== '' ? ' for <strong>' . $business . '</strong>' : '') . '.</p>'
                . '<p>Your application is <strong>in review</strong>. Our team will contact you once a decision is made.</p>'
                . '<p style="margin-top:20px;">Regards,<br><strong>Rafi &amp; Sons</strong></p>'
            )
        );
    }

    public function sendApplicationApproved(array $vendor): void
    {
        $email = trim((string) ($vendor['email'] ?? ''));
        if ($email === '') {
            return;
        }

        $name = htmlspecialchars((string) ($vendor['contact_name'] ?? 'Vendor'));
        $loginUrl = site_url('vendor/login');

        $this->sendHtml(
            $email,
            'Your vendor application has been approved',
            $this->wrap(
                '<h2 style="color:#c8102e;margin:0 0 12px;">Application approved</h2>'
                . '<p>Dear ' . $name . ',</p>'
                . '<p>Good news — your vendor application has been <strong>approved</strong>.</p>'
                . '<p>You can now sign in to your vendor portal using the email and password you registered with:</p>'
                . '<p><a href="' . htmlspecialchars($loginUrl) . '" style="display:inline-block;background:#c8102e;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;">Login to Vendor Panel</a></p>'
                . '<p style="margin-top:20px;">Regards,<br><strong>Rafi &amp; Sons</strong></p>'
            )
        );
    }

    public function sendApplicationRejected(array $vendor): void
    {
        $email = trim((string) ($vendor['email'] ?? ''));
        if ($email === '') {
            return;
        }

        $name = htmlspecialchars((string) ($vendor['contact_name'] ?? 'Vendor'));

        $this->sendHtml(
            $email,
            'Update on your vendor application',
            $this->wrap(
                '<h2 style="color:#c8102e;margin:0 0 12px;">Application update</h2>'
                . '<p>Dear ' . $name . ',</p>'
                . '<p>Thank you for your interest. Unfortunately we are unable to approve your vendor application at this time.</p>'
                . '<p style="margin-top:20px;">Regards,<br><strong>Rafi &amp; Sons</strong></p>'
            )
        );
    }

    private function sendHtml(string $to, string $subject, string $body): void
    {
        $config = $this->resolveMailConfig();
        if ($config['from_email'] === '' || $config['host'] === '') {
            log_message('info', 'Vendor email skipped: SMTP not configured.');

            return;
        }

        $email = Services::email();
        $email->clear(true);
        $email->initialize([
            'protocol'    => 'smtp',
            'SMTPHost'    => $config['host'],
            'SMTPUser'    => $config['user'],
            'SMTPPass'    => $config['pass'],
            'SMTPPort'    => $config['port'],
            'SMTPCrypto'  => $config['crypto'],
            'SMTPTimeout' => 15,
            'mailType'    => 'html',
            'charset'     => 'UTF-8',
            'newline'     => "\r\n",
            'CRLF'        => "\r\n",
        ]);
        $email->setFrom($config['from_email'], $config['from_name']);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($body);

        if (! $email->send(false)) {
            log_message('error', 'Vendor email to ' . $to . ' failed: ' . $email->printDebugger(['headers']));
        }
    }

    /**
     * @return array{host:string,user:string,pass:string,port:int,crypto:string,from_email:string,from_name:string}
     */
    private function resolveMailConfig(): array
    {
        $settings = model(SettingModel::class)->getMap();

        $host = trim((string) (env('email.SMTPHost') ?: ($settings['smtp_host'] ?? '')));
        $user = trim((string) (env('email.SMTPUser') ?: ($settings['smtp_user'] ?? '')));
        $pass = (string) (env('email.SMTPPass') ?: ($settings['smtp_pass'] ?? ''));
        $port = (int) (env('email.SMTPPort') ?: ($settings['smtp_port'] ?? 465));
        $crypto = trim((string) (env('email.SMTPCrypto') ?: ($settings['smtp_crypto'] ?? 'ssl')));
        $fromEmail = trim((string) (env('email.fromEmail') ?: ($settings['smtp_from_email'] ?? $user)));
        $fromName = trim((string) (env('email.fromName') ?: ($settings['smtp_from_name'] ?? 'Rafi & Sons')));

        if ($crypto === '' && $port === 465) {
            $crypto = 'ssl';
        }

        return [
            'host'       => $host,
            'user'       => $user,
            'pass'       => $pass,
            'port'       => $port > 0 ? $port : 465,
            'crypto'     => $crypto,
            'from_email' => $fromEmail,
            'from_name'  => $fromName,
        ];
    }

    private function wrap(string $inner): string
    {
        return '<div style="font-family:Arial,sans-serif;max-width:560px;margin:0 auto;color:#222;line-height:1.5;">'
            . $inner
            . '</div>';
    }
}
