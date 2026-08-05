<?php

namespace App\Libraries;

use App\Models\SettingModel;
use Config\Services;

class OrderMailService
{
    /**
     * @param array<string, mixed> $order
     * @param list<array<string, mixed>> $items
     */
    public function sendOrderEmails(array $order, array $items): void
    {
        $config = $this->resolveMailConfig();
        if ($config['from_email'] === '' || $config['host'] === '') {
            log_message('info', 'Order email skipped: SMTP not configured.');

            return;
        }

        $customerEmail = trim((string) ($order['customer_email'] ?? ''));
        $notifyTo = $config['notify_email'];
        $orderNumber = (string) ($order['order_number'] ?? '');

        if ($customerEmail !== '') {
            $this->send(
                $config,
                $customerEmail,
                'Order Received — ' . $orderNumber,
                $this->buildCustomerHtml($order, $items)
            );
        }

        if ($notifyTo !== '') {
            $this->send(
                $config,
                $notifyTo,
                '[New Order] ' . $orderNumber,
                $this->buildAdminHtml($order, $items)
            );
        }
    }

    /**
     * @return array{host:string,user:string,pass:string,port:int,crypto:string,from_email:string,from_name:string,notify_email:string}
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
        $notifyEmail = trim((string) (env('email.orderNotify') ?: ($settings['order_notify_email'] ?? $fromEmail)));

        if ($crypto === '' && $port === 465) {
            $crypto = 'ssl';
        }

        return [
            'host'        => $host,
            'user'        => $user,
            'pass'        => $pass,
            'port'        => $port > 0 ? $port : 465,
            'crypto'      => $crypto,
            'from_email'  => $fromEmail,
            'from_name'   => $fromName,
            'notify_email'=> $notifyEmail,
        ];
    }

    /**
     * @param array{host:string,user:string,pass:string,port:int,crypto:string,from_email:string,from_name:string,notify_email:string} $config
     */
    private function send(array $config, string $to, string $subject, string $body): void
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
            log_message('error', 'Order email to ' . $to . ' failed: ' . $email->printDebugger(['headers']));
        }
    }

    /**
     * @param array<string, mixed> $order
     * @param list<array<string, mixed>> $items
     */
    private function buildCustomerHtml(array $order, array $items): string
    {
        $orderNumber = htmlspecialchars((string) ($order['order_number'] ?? ''));
        $name = htmlspecialchars((string) ($order['customer_name'] ?? 'Customer'));
        $dueNow = number_format((float) ($order['subtotal'] ?? 0), 0);
        $grand = number_format((float) ($order['total_payable'] ?? $order['subtotal'] ?? 0), 0);

        return $this->wrap(
            '<h2 style="color:#c8102e;margin:0 0 12px;">Thank you — we received your order</h2>'
            . '<p>Hi ' . $name . ',</p>'
            . '<p>Your order <strong>' . $orderNumber . '</strong> has been received successfully. Our team will contact you shortly to confirm the details.</p>'
            . $this->itemsTable($items)
            . '<p><strong>Due now:</strong> PKR ' . $dueNow . '<br>'
            . '<strong>Total order value:</strong> PKR ' . $grand . '</p>'
            . '<p style="color:#666;font-size:13px;">If you did not place this order, please contact us immediately.</p>'
            . '<p style="margin-top:20px;">Regards,<br><strong>Rafi &amp; Sons</strong></p>'
        );
    }

    /**
     * @param array<string, mixed> $order
     * @param list<array<string, mixed>> $items
     */
    private function buildAdminHtml(array $order, array $items): string
    {
        $orderNumber = htmlspecialchars((string) ($order['order_number'] ?? ''));
        $dueNow = number_format((float) ($order['subtotal'] ?? 0), 0);
        $grand = number_format((float) ($order['total_payable'] ?? $order['subtotal'] ?? 0), 0);

        $details = '<ul style="margin:0 0 16px;padding-left:18px;line-height:1.7;">'
            . '<li><strong>Customer:</strong> ' . htmlspecialchars((string) ($order['customer_name'] ?? '')) . '</li>'
            . '<li><strong>Phone:</strong> ' . htmlspecialchars((string) ($order['customer_phone'] ?? '')) . '</li>'
            . '<li><strong>Email:</strong> ' . htmlspecialchars((string) ($order['customer_email'] ?? '—')) . '</li>'
            . '<li><strong>CNIC:</strong> ' . htmlspecialchars((string) ($order['customer_cnic'] ?? '—')) . '</li>'
            . '<li><strong>City:</strong> ' . htmlspecialchars((string) ($order['customer_city'] ?? '—')) . '</li>'
            . '<li><strong>Address:</strong> ' . htmlspecialchars((string) ($order['customer_address'] ?? '—')) . '</li>'
            . '<li><strong>Payment type:</strong> ' . htmlspecialchars((string) ($order['payment_type'] ?? 'cash')) . '</li>'
            . '</ul>';

        $notes = trim((string) ($order['admin_notes'] ?? ''));
        $notesBlock = $notes !== ''
            ? '<p><strong>Customer notes:</strong><br>' . nl2br(htmlspecialchars($notes)) . '</p>'
            : '';

        return $this->wrap(
            '<h2 style="color:#1a3a5c;margin:0 0 12px;">New order received</h2>'
            . '<p>Order number: <strong>' . $orderNumber . '</strong></p>'
            . $details
            . $this->itemsTable($items)
            . '<p><strong>Due now:</strong> PKR ' . $dueNow . '<br>'
            . '<strong>Total order value:</strong> PKR ' . $grand . '</p>'
            . $notesBlock
        );
    }

    /**
     * @param list<array<string, mixed>> $items
     */
    private function itemsTable(array $items): string
    {
        $rows = '';
        foreach ($items as $item) {
            $mode = ($item['payment_type'] ?? 'cash') === 'installment' ? 'Installment' : 'Cash';
            $detail = $mode === 'Installment'
                ? sprintf(
                    'Advance PKR %s · PKR %s × %s mo · Total PKR %s',
                    number_format((float) ($item['down_payment'] ?? 0), 0),
                    number_format((float) ($item['monthly_installment'] ?? 0), 0),
                    (int) ($item['months'] ?? 0),
                    number_format((float) ($item['total_payable'] ?? 0), 0)
                )
                : 'Cash PKR ' . number_format((float) ($item['cash_price'] ?? $item['unit_price']), 0);

            $rows .= '<tr>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars((string) $item['product_name']) . ' × ' . (int) $item['quantity'] . '</td>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;">' . htmlspecialchars($mode) . '<br><small>' . htmlspecialchars($detail) . '</small></td>'
                . '<td style="padding:8px;border-bottom:1px solid #eee;text-align:right;">PKR ' . number_format((float) $item['line_total'], 0) . '</td>'
                . '</tr>';
        }

        return '<table style="width:100%;border-collapse:collapse;margin:16px 0;">'
            . '<thead><tr>'
            . '<th style="text-align:left;padding:8px;border-bottom:2px solid #ddd;">Product</th>'
            . '<th style="text-align:left;padding:8px;border-bottom:2px solid #ddd;">Payment</th>'
            . '<th style="text-align:right;padding:8px;border-bottom:2px solid #ddd;">Due now</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table>';
    }

    private function wrap(string $inner): string
    {
        return '<div style="font-family:Arial,sans-serif;color:#222;max-width:620px;line-height:1.5;">' . $inner . '</div>';
    }
}
