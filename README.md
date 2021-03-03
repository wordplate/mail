# Mail

> A custom [SMTP](https://developer.wordpress.org/reference/hooks/phpmailer_init/) credentials plugin for WordPress.

To send email with WordPress you can use the [`wp_mail`](https://developer.wordpress.org/reference/functions/wp_mail) helper method. WordPlate provides a simple way to add custom SMTP credentials.

[![Monthly Downloads](https://badgen.net/packagist/dm/wordplate/mail)](https://packagist.org/packages/wordplate/mail/stats)
[![Latest Version](https://badgen.net/packagist/v/wordplate/mail)](https://packagist.org/packages/wordplate/mail)

## Installation

Require the package, with Composer, in the root directory of your project.

```sh
composer require wordplate/mail
```

Then update the credentials in your `.env` environment file with your SMTP keys.

```
MAIL_HOST=null
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=null
```

Then login to the WordPress administrator dashboard and active the plugin.

