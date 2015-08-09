<?php
namespace Six_x;
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
 * Zipped class
 *
 * @package		six-x
 * @subpackage	database
 * @category	Database
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/guide/database/
 */

class Cirilic {

    /**
     * Convert from cirilic to en trascription
     *
     * @return	string	(binary encoded)
     */
    public static function Transcript($string)
    {
        $cirilic = ["а","б","в","г","д","е","ё","ж","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х","ц","ч","ш","щ","ъ","ы","ь","э","ю","я",
            "А","Б","В","Г","Д","Е","Ё","Ж","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я"];
        $transcript =["a","b","v","g","d","e","e","j","z","i","i","k","l","m","n","o","p","r","s","t","u","f","h","c","ch","sh","sh?","?","?","?","a","u","ja",
            "A","B","V","G","D","E","E","J","Z","I","I","K","L","M","N","O","P","R","S","T","U","F","H","C","CH","SH","SH?","?","?","?","A","U","JA"];
        return str_replace($cirilic, $transcript, $string);
    }
}


/* End of file cirilic.php */
/* Location: ./system/library/cirilic.php */