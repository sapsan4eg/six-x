<?php
namespace Six_x;
    /**
     * Six-X
     *
     * An open source application development framework for PHP 5.3.0 or newer
     *
     * @package		six-x
     * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
     * @copyright	Copyright (c) 2014 - 2014, Yuri Nasyrov.
     * @license		http://six-x.org/guide/license.html
     * @link		http://six-x.org
     * @since		Version 1.0.0.0
     * @filesource
     */

// ------------------------------------------------------------------------

/**
 * Arrays Class
 *
 * primitive functions with arrays
 *
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Arrays
{
    protected $_array = [];
    protected static $_from;
    protected static $_to;
    protected static $_search;

    // --------------------------------------------------------------------

    /**
     * return array
     *
     * @return	array
     */
    public function toArray()
    {
        return (array)$this->_array;
    }

    // --------------------------------------------------------------------

    /**
     * extract a slice of the array
     *
     * @param   int
     * @param   int
     * @return	object
     */
    public function limit($from, $count = NULL)
    {
        $count = empty($count) ? $from >= 0 ? (int)$from : 0 : (int)$count;
        $from = $count == $from ? 0 : (int)$from;
        $this->_array = array_slice((array)$this->_array, $from, $count);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     *  convert string to requested character encoding
     *
     * @param   string
     * @param   string
     * @return	object
     */
    public function iconv($from, $to)
    {
        Arrays::$_from  = $from;
        Arrays::$_to    = $to;
        $this->_array   = array_map(['Six_x\Arrays', '_iconv'], (array)$this->_array);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * callback function to convert string to requested character encoding
     *
     * @param   string
     * @return	string
     */
    protected static function _iconv($string)
    {
        if(is_array($string)) return array_map(['Six_x\Arrays', '_iconv'], $string);
        return iconv(Arrays::$_from, Arrays::$_to, $string);
    }

    // --------------------------------------------------------------------

    /**
     *  check contains string into array
     *
     * @param   string
     * @return	object
     */
    public function contains($string)
    {
        Arrays::$_search = $string;
        $this->_array = array_filter((array)$this->_array, ['Six_x\Arrays', '_contains']);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * callback function to check contains string into array
     *
     * @param   string
     * @return	bool
     */
    protected static function _contains($string)
    {
        if(is_array($string)) return array_filter($string, ['Six_x\Arrays', '_contains']);
        return strpos($string, Arrays::$_search) > -1;
    }

    // --------------------------------------------------------------------

    /**
     *  Split an array into pages
     *
     * @return	array
     */
    public function pages($count = NULL)
    {
        $count = empty($count) ? 10 : (int)$count;
        $this->_array = array_chunk((array)$this->_array, $count);
        return $this;
    }
}

/* End of file array.php */
/* Location: ./system/library/array.php */
