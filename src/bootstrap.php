<?php
/**
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Georges.L (Geolim4) <contact@geolim4.com>
 *
 */


/**
 * Hard includes
 * @todo Autoload :)
 */
setlocale(LC_ALL, 'fr_FR.utf8');

require __DIR__ . './../vendor/autoload.php';
require __DIR__ . './../vendor_external/simple_html_dom.php';
require __DIR__ . './inc/OvhTask.php';
require __DIR__ . './inc/OvhRule.php';
require __DIR__ . './inc/Helper.php';

const OVH_TRAVAUX_URL = 'http://travaux.ovh.net';
const SRC_DIR = __DIR__ . DIRECTORY_SEPARATOR;
const ROOT_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
const CACHE_DIR =  ROOT_DIR . 'var/cache' . DIRECTORY_SEPARATOR;
const CONFIG_DIR = ROOT_DIR . 'var/config' . DIRECTORY_SEPARATOR;
const DELAY_BETWEEN_IN_PROGRESS_ALERTS = 900;//In seconds
const DELAY_BETWEEN_PLANNED_ALERTS = 86400;//In seconds