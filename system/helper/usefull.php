<?php
/**
 * Six-X
 *
 * An open source application development framework for PHP 5.4.0 or newer
 *
 * @package		six-x
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @copyright	Copyright (c) 2014 - 2015, Yuri Nasyrov.
 * @license		http://six-x.org/guide/license.html
 * @link		http://six-x.org
 * @since		Version 1.0.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Translate label
 *
 * @param $label
 * @return string
 */
function _($label)
{
    return \Six_x\Mui::get($label);
}

/**
 * Validate date format
 *
 * @param $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
/**
 * Return html cleared string
 *
 * @param string    $string
 * @return string
 */
function strip_html($string)
{
    $search = ['@<script[^>]*?>.*?</script>@si',  // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',                  // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',          // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'               // Strip multi-line comments including CDATA
    ];

    $string = preg_replace($search, '', $string);
    $string = str_replace([PHP_EOL, '	', '\r\n', '\n', '\r', '\v', '\t', '\e', '\f', '&nbsp;', '&copy;', '&mdash;', '0x0A', '0x0D', '0x09', '0x0B', ' 0x1B ' ], ' ', $string);

    while(true) {
        if(strpos($string, '  ') === FALSE) {
            break;
        }
        $string = str_replace('  ', ' ', $string);
    }

    return $string;
}