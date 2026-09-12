<?php

namespace App\Libraries;

/**
 * Google reCAPTCHA v3 verification.
 */
class RecaptchaService
{
    protected string $siteKey;
    protected string $secretKey;
    protected float $minScore;

    public function __construct()
    {
        $this->siteKey   = trim((string) env('recaptcha.siteKey', ''));
        $this->secretKey = trim((string) env('recaptcha.secretKey', ''));
        $this->minScore  = (float) env('recaptcha.minScore', 0.5);
    }

    public function isEnabled(): bool
    {
        return $this->siteKey !== '' && $this->secretKey !== '';
    }

    public function siteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * @return array{ok:bool, message?:string, score?:float, action?:string}
     */
    public function verify(?string $token, string $expectedAction = '', ?string $remoteIp = null): array
    {
        if (! $this->isEnabled()) {
            return ['ok' => true];
        }

        $token = trim((string) $token);
        if ($token === '') {
            return ['ok' => false, 'message' => 'Security check failed. Please refresh and try again.'];
        }

        try {
            $client = \Config\Services::curlrequest([
                'timeout'     => 8,
                'http_errors' => false,
            ]);

            $payload = [
                'secret'   => $this->secretKey,
                'response' => $token,
            ];
            if ($remoteIp) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            if (! is_array($body)) {
                return ['ok' => false, 'message' => 'Security check unavailable. Please try again.'];
            }

            if (empty($body['success'])) {
                return ['ok' => false, 'message' => 'Security check failed. Please try again.'];
            }

            $score = isset($body['score']) ? (float) $body['score'] : 0.0;
            if ($score < $this->minScore) {
                return [
                    'ok'      => false,
                    'message' => 'Security check failed. Please try again.',
                    'score'   => $score,
                ];
            }

            $action = (string) ($body['action'] ?? '');
            if ($expectedAction !== '' && $action !== $expectedAction) {
                return [
                    'ok'      => false,
                    'message' => 'Security check failed. Please try again.',
                    'score'   => $score,
                    'action'  => $action,
                ];
            }

            return [
                'ok'     => true,
                'score'  => $score,
                'action' => $action,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'reCAPTCHA verify failed: ' . $e->getMessage());

            return ['ok' => false, 'message' => 'Security check unavailable. Please try again.'];
        }
    }
}
