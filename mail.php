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
 * Version: 3.0.0
 * Plugin URI: https://github.com/wordplate/mail#readme
 */

declare(strict_types=1);

// If the environment function doesn't exists, we don't want to continue.
if (!function_exists('env')) {
    return;
}

/*
 * Set custom smtp credentials.
 */
add_action('phpmailer_init', function (PHPMailer $mail) {
    $mail->IsSMTP();
    $mail->SMTPAuth = env('MAIL_USERNAME') && env('MAIL_PASSWORD');

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
