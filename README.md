# Mail

![mail](https://user-images.githubusercontent.com/499192/37464248-1282c40a-2858-11e8-801c-571c1dedf310.png)

> A [mail](https://codex.wordpress.org/Plugin_API/Action_Reference/phpmailer_init) plugin for [WordPlate](https://wordplate.github.io/docs/mail).

To send email with WordPress you can use the [`wp_mail`](https://developer.wordpress.org/reference/functions/wp_mail) helper method. WordPlate provides a simple way to add custom SMTP credentials and easier working with attachments.

[![Monthly Downloads](https://badgen.net/packagist/dm/wordplate/mail)](https://packagist.org/packages/wordplate/mail/stats)
[![Latest Version](https://badgen.net/packagist/v/wordplate/mail)](https://packagist.org/packages/wordplate/mail)

## Installation

Require the [mail package](https://github.com/wordplate/mail#readme), with [Composer](https://getcomposer.org), in the root directory of your project.

```sh
$ composer require wordplate/mail
```

Then update the credentials in your `.env` environment file with your SMTP keys.

```
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

Then login to the WordPress administrator dashboard and active the plugin.

#### Name & Address

Optionally you may also specify the global "from" address and name.

```
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=null
```

Please visit the [WordPress documentation](https://developer.wordpress.org/reference/hooks/phpmailer_init) to read more about the `phpmailer_init` action hook.

## Attachments

Generally [`wp_mail`](https://developer.wordpress.org/reference/functions/wp_mail) only accept a flat array with filenames (or comma separated string), the WordPlate mail plugin allows you to send all variables accepted by [PHPMailer](https://github.com/PHPMailer/PHPMailer#a-simple-example), like name, encoding & disposition. The plugin accepts both the old way, with flat array, as well as the the new format, see example below:

```php
wp_mail('marty@mcfly.se', 'Time Travel', 'This is heavy', '', [
    [
        'path' => __DIR__.'/images/beer-image-v1.jpg',
        'name' => 'beer.jpg',
        'encoding' => '8bit',
        'type' => 'image/jpeg',
        'disposition' => 'attachment',
    ],
    [
        'path' => __DIR__.'/images/logo.png',
        'name' => 'logo.jpg',
        'encoding' => 'base64',
        'type' => 'image/png',
        'disposition' => 'inline',
    ],
]);
```

## License

[MIT](LICENSE) © [Vincent Klaiber](https://doubledip.se)
