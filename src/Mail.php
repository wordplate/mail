<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/wordplate/mail
 */

declare(strict_types=1);

namespace WordPlate\Mail;

use PHPMailer;
use phpmailerException;

final class Mail
{
    /**
     * @var array
     */
    protected $attachments;

    public function initialize(): void
    {
        add_action('phpmailer_init', [$this, 'setCustomCredentials']);

        add_filter('wp_mail_from', [$this, 'filterMailFromAddress']);
        add_filter('wp_mail_from_name', [$this, 'filterMailFromName']);

        add_filter('wp_mail', [$this, 'filterMailAttachments'], PHP_INT_MAX);
    }

    public function setCustomCredentials(PHPMailer $mail): void
    {
        $mail->IsSMTP();
        $mail->SMTPAutoTLS = false;

        if (function_exists('env')) {
            $mail->SMTPAuth = env('MAIL_USERNAME') && env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');

            $mail->Host = env('MAIL_HOST');
            $mail->Port = env('MAIL_PORT', 587);
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
        }
    }

    public function filterMailFromAddress(string $mailFromAddress): string
    {
        if (defined('MAIL_FROM_ADDRESS')) {
            return MAIL_FROM_ADDRESS;
        }

        if (function_exists('env') && env('MAIL_FROM_ADDRESS')) {
            define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'));

            return MAIL_FROM_ADDRESS;
        }

        return $mailFromAddress;
    }

    public function filterMailFromName(string $mailFromName): string
    {
        if (defined('MAIL_FROM_NAME')) {
            return MAIL_FROM_NAME;
        }

        if (function_exists('env') && env('MAIL_FROM_NAME')) {
            define('MAIL_FROM_NAME', env('MAIL_FROM_NAME'));

            return MAIL_FROM_NAME;
        }

        return $mailFromName;
    }

    public function filterMailAttachments(array $args): array
    {
        // Add ability to override the attachment name in wp_mail() when adding
        // attachments.
        if (empty($args['attachments']) || !is_array($args['attachments'])) {
            return $args;
        }

        // Prepare new attachments array.
        $this->attachments = array_map(function ($attachment) {
            if (!is_array($attachment)) {
                return [
                    'path' => $attachment,
                    'name' => '',
                    'encoding' => 'base64',
                    'type' => '',
                    'disposition' => 'attachment',
                ];
            }

            return wp_parse_args($attachment, [
                'path' => null,
                'name' => '',
                'encoding' => 'base64',
                'type' => '',
                'disposition' => 'attachment',
            ]);
        }, $args['attachments']);

        // Empty attachments and add them in the PHPMailer hook.
        $args['attachments'] = [];

        add_action('phpmailer_init', [$this, 'addMailAttachments'], PHP_INT_MAX);

        // Remove listener if mail failed.
        add_action('wp_mail_failed', function ($error) {
            remove_action('phpmailer_init', [$this, 'addMailAttachments'], PHP_INT_MAX);
        });

        return $args;
    }

    public function addMailAttachments(PHPMailer $mail): void
    {
        // Add ability to override the attachment name in wp_mail() when adding
        // attachments.
        remove_action('phpmailer_init', [$this, 'addMailAttachments'], PHP_INT_MAX);

        if (empty($this->attachments)) {
            return;
        }

        foreach ($this->attachments as $attachment) {
            try {
                $mail->addAttachment(
                    $attachment['path'],
                    $attachment['name'],
                    $attachment['encoding'],
                    $attachment['type'],
                    $attachment['disposition']
                );
            } catch (phpmailerException $ignored) {
                continue;
            }
        }
    }
}
