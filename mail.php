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

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly
}

require(__DIR__.'/src/Mail.php');

(new \WordPlate\Mail())->initialize();
