<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/wordplate/mail
 */

/*
 * Plugin Name: Mail
 * Description: A custom SMTP credentials plugin for WordPress.
 * Author: WordPlate
 * Author URI: https://github.com/wordplate/wordplate
 * Version: 7.1.0
 * Plugin URI: https://github.com/wordplate/mail
 */

use PHPMailer\PHPMailer\PHPMailer;

function mail_credentials(PHPMailer $mail)
{
    $mail->IsSMTP();
    $mail->SMTPAutoTLS = false;

    $mail->SMTPAuth = true;
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';

    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->Port = $_ENV['MAIL_PORT'] ?? 587;
    $mail->Username = $_ENV['MAIL_USERNAME'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];

    return $mail;
}

add_action('phpmailer_init', 'mail_credentials');

function mail_content_type()
{
    return 'text/html';
}

add_filter('wp_mail_content_type', 'mail_content_type');

if (isset($_ENV['MAIL_FROM_ADDRESS'])) {
    function mail_from_address()
    {
        return $_ENV['MAIL_FROM_ADDRESS'];
    }

    add_filter('wp_mail_from', 'mail_from_address');
}

if (isset($_ENV['MAIL_FROM_NAME'])) {
    function mail_from_name()
    {
        return $_ENV['MAIL_FROM_NAME'];
    }

    add_filter('wp_mail_from_name', 'mail_from_name');
}
