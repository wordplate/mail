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
 * Version: 1.0.0
 * Plugin URI: https://github.com/wordplate/multisite
 */

 /*
  * Set custom smtp credentials.
  */
 add_action('phpmailer_init', function (PHPMailer $mail) {
     $mail->IsSMTP();
     $mail->SMTPAuth = true;

     $mail->Host = env('MAIL_HOST');
     $mail->Port = env('MAIL_PORT', 587);
     $mail->Username = env('MAIL_USERNAME');
     $mail->Password = env('MAIL_PASSWORD');

     return $mail;
 });

 /*
 * Set text/html content type for emails.
 */
add_filter('wp_mail_content_type', function () {
    return 'text/html';
});
