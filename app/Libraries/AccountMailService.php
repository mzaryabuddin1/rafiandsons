<?php

namespace App\Libraries;

use App\Models\SettingModel;
use Config\Services;

class AccountMailService
{
    public function sendOtp(string $to, string $otp, string $purposeLabel): bool
    {
        $config = $this->resolveMailConfig();
        if ($config['from_email'] === '' || $config['host'] === '') {
            log_message('error', 'OTP email skipped: SMTP not configured.');

            return false;
        }

        $subject = $purposeLabel . ' — Rafi & Sons';
        $body = $this->wrap(
            '<h2 style="color:#c8102e;margin:0 0 12px;">' . htmlspecialchars($purposeLabel) . '</h2>'
            . '<p>Use this one-time code to continue:</p>'
            . '<p style="font-size:32px;font-weight:700;letter-spacing:8px;margin:20px 0;color:#1e293b;">'
            . htmlspecialchars($otp)
            . '</p>'
            . '<p style="color:#666;font-size:13px;">This code expires in 10 minutes. Do not share it with anyone.</p>'
            . '<p style="margin-top:20px;">Regards,<br><strong>Rafi &amp; Sons</strong></p>'
        );

        return $this->send($config, $to, $subject, $body);
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

    /**
     * @param array{host:string,user:string,pass:string,port:int,crypto:string,from_email:string,from_name:string} $config
     */
    private function send(array $config, string $to, string $subject, string $body): bool
    {
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
            log_message('error', 'Account email to ' . $to . ' failed: ' . $email->printDebugger(['headers']));

            return false;
        }

        return true;
    }

    private function wrap(string $inner): string
    {
        return '<div style="font-family:Arial,sans-serif;color:#222;max-width:620px;line-height:1.5;">' . $inner . '</div>';
    }
}
