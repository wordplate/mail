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

// If the environment function doesn't exist, we don't want to continue.
if (!function_exists('env')) {
    return;
}

// Add custom SMTP credentials.
add_action('phpmailer_init', function (PHPMailer $mail) {
    $mail->IsSMTP();
    $mail->SMTPAuth = env('MAIL_USERNAME') && env('MAIL_PASSWORD');

    $mail->SMTPAutoTLS = false;
    $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');

    $mail->Host = env('MAIL_HOST');
    $mail->Port = env('MAIL_PORT', 587);
    $mail->Username = env('MAIL_USERNAME');
    $mail->Password = env('MAIL_PASSWORD');

    return $mail;
});

// Add filter for default mail from address, if defined.
if (env('MAIL_FROM_ADDRESS')) {
    define('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'));

    add_filter('wp_mail_from', function () {
        return MAIL_FROM_ADDRESS;
    });
}

// Add filter for default mail from name, if defined.
if (env('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', env('MAIL_FROM_NAME'));

    add_filter('wp_mail_from_name', function () {
        return MAIL_FROM_NAME;
    });
}

// Add abilit to override the attachment name in wp_mail() when adding attachments.
add_filter('wp_mail', function ($args) {
    if (!isset($args['attachments']) || !is_array($args['attachments'])) {
        return $args;
    }

    // Prepare new attachment array
    $attachments = array_map(function ($attachment) {
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

    // Do nothing if attachments array is empty.
    if (empty($attachments)) {
        return $args;
    }

    // Empty attachments and add them in the PHPMailer hook.
    $args['attachments'] = [];

    add_action('phpmailer_init', $callback = function (PHPMailer $mail) use ($attachments, &$callback) {
        remove_action('phpmailer_init', $callback, PHP_INT_MAX);

        if (empty($attachments)) {
            return;
        }

        foreach ($attachments as $attachment) {
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
    }, PHP_INT_MAX);

    return $args;
}, PHP_INT_MAX);
