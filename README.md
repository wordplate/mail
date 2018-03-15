# Mail

> A [mail](https://codex.wordpress.org/Plugin_API/Action_Reference/phpmailer_init) plugin for [WordPlate](https://wordplate.github.io/docs/mail).

[![StyleCI](https://styleci.io/repos/57282597/shield?style=flat)](https://styleci.io/repos/57282597)
[![Total Downloads](https://img.shields.io/packagist/dt/wordplate/mail.svg?style=flat)](https://packagist.org/packages/wordplate/mail)
[![Latest Version](https://img.shields.io/github/release/wordplate/mail.svg?style=flat)](https://github.com/wordplate/mail/releases)
[![License](https://img.shields.io/packagist/l/wordplate/mail.svg?style=flat)](https://packagist.org/packages/wordplate/mail)

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

Optionally you may also specify the global "from" address and name.

```
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=null
```

Please visit the [WordPress documentation](https://developer.wordpress.org/reference/hooks/phpmailer_init) to read more about the `phpmailer_init` action hook.

## License

[MIT](LICENSE) © [Vincent Klaiber](https://vinkla.com)
