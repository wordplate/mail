<?php

/*
 * This file is part of WordPlate.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Plugin Name: Mail
 * Description: A mail plugin for WordPlate.
 * Author: WordPlate
 * Author URI: https://wordplate.github.io
 * Version: 3.2.0
 * Plugin URI: https://github.com/wordplate/mail#readme
 */

declare(strict_types=1);

namespace WordPlate;

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly
}

// If the environment function doesn't exist, we don't want to continue.
if (!function_exists('env')) {
    return;
}

/**
 * This is the mail class.
 *
 * @author Daniel Gerdgren <daniel@gerdgren.se>
 * @author Vincent Klaiber <hello@vinkla.com>
 */
final class Mail
{

    /**
     * Processed attachments.
     *
     * @var array
     */
    protected $attachments;

    /**
     * Initialize.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('phpmailer_init', [$this, 'setCustomCredentials']);

        add_filter('wp_mail_from', [$this, 'filterMailFromAddress']);
        add_filter('wp_mail_from_name', [$this, 'filterMailFromName']);

        add_filter('wp_mail', [$this, 'filterMailAttachments'], PHP_INT_MAX);
    }

    /**
     * Set custom SMTP credentials.
     *
     * @param \PHPMailer $mail
     *
     * @return \PHPMailer
     */
    public function setCustomCredentials(\PHPMailer $mail)
    {
        $mail->IsSMTP();
        $mail->SMTPAuth = env('MAIL_USERNAME') && env('MAIL_PASSWORD');

        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');

        $mail->Host = env('MAIL_HOST');
        $mail->Port = env('MAIL_PORT', 587);
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('MAIL_PASSWORD');

        return $mail;
    }

    /**
     * Filter mail from address.
     *
     * @param string $mailFromAddress
     *
     * @return string
     */
    public function filterMailFromAddress($mailFromAddress)
    {
        if (defined('MAIL_FROM_ADDRESS')) {
            return MAIL_FROM_ADDRESS;
        } else if (env('MAIL_FROM_ADDRESS')) {
            define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'));
            return MAIL_FROM_ADDRESS;
        }

        return $mailFromAddress;
    }

    /**
     * Filter mail from name.
     *
     * @param string $mailFromName
     *
     * @return string
     */
    public function filterMailFromName($mailFromName)
    {
        if (defined('MAIL_FROM_NAME')) {
            return MAIL_FROM_NAME;
        } else if (env('MAIL_FROM_NAME')) {
            define('MAIL_FROM_NAME', env('MAIL_FROM_NAME'));
            return MAIL_FROM_NAME;
        }

        return $mailFromName;
    }

    /**
     * Add ability to override the attachment name in wp_mail() when adding attachments.
     *
     * @param array $args
     *
     * @return array
     */
    public function filterMailAttachments($args)
    {
        if (empty($args['attachments']) || !is_array($args['attachments'])) {
            return $args;
        }

        // Prepare new attachments array
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

        // Remove listner if mail failed
        add_action('wp_mail_failed', function ($error) {
            remove_action('phpmailer_init', [$this, 'addMailAttachments'], PHP_INT_MAX);
        });

        return $args;
    }

    /**
     * Add ability to override the attachment name in wp_mail() when adding attachments.
     *
     * @param \PHPMailer $mail
     *
     * @return \PHPMailer
     */
    public function addMailAttachments(\PHPMailer $mail)
    {
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
            } catch (\phpmailerException $ignored) {
                continue;
            }
        }
    }
}

(new Mail())->initialize();
