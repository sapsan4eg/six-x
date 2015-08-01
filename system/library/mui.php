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
 * Mui Class
 *
 * support multi language interface of application
 *
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Mui {
    protected static $_default = 'en';
    protected static $_lang;
    protected static $_dictionary = array();
    protected static $_name;
    protected static $_list_languges = array();
    protected static $db;

    /**
     * initiation
     *
     * @param	object
     * @param	array
     * @param	array
     * @param	object
     * @param	array
     * @param	array
     */
    public static function start($db, $arguments, $post, $session, $cookie, $server)
    {
        self::$db = $db;
        // get default language
        self::$_lang = self::$_default;

        // name of default language
        self::$_name = 'no name';

        // check in database supports languages
        $query = self::$db->query("SELECT * FROM `" . DB_PREFIX . "language`
										WHERE status = '1'
										ORDER BY `sort_order`");
        // create associative array type => name
        self::_set_list_languges($query);

        // check what language need
        if($query->count > 1)
        {
            $languages = array();

            // loop through result list
            foreach ($query->list As $result)
            {
                $languages[$result['lang']] = $result;
            }

            // array possible language accepted
            $array = array(
                isset($arguments['my_mui_language']) ? $arguments['my_mui_language'] : FALSE,
                isset($post['my_mui_language']) ? $post['my_mui_language'] : FALSE,
                isset($session->data['language']) ? $session->data['language'] : FALSE,
                isset($cookie['language']) ? $cookie['language'] : FALSE
            );

            // need check browser to get language locale
            $check_browser = TRUE;

            // loop throught possible array
            foreach($array As $value)
            {
                if ($value != FALSE && array_key_exists($value, $languages) && $languages[$value]['status'])
                {
                    self::$_name	= $languages[$value]['name'];
                    self::$_lang	= $value;
                    $check_browser	= FALSE;
                    break;
                }
            }

            // if need check browser to language locale
            if($check_browser)
            {
                if (isset($server['HTTP_ACCEPT_LANGUAGE']))
                {
                    if($server['HTTP_ACCEPT_LANGUAGE'])
                    {
                        $browser_languages = explode(',', $server['HTTP_ACCEPT_LANGUAGE']);

                        foreach ($browser_languages As $browser_language)
                        {
                            foreach ($languages As $key => $value)
                            {
                                if ($value['status'])
                                {
                                    $locale = explode(',', $value['locale']);

                                    if (in_array($browser_language, $locale))
                                    {
                                        self::$_lang = $key;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // ------------------------------------------------------------------------

        }
        else if ($query->count == 1)
        {
            self::$_name = $query->first['name'];
            self::$_lang = $query->first['lang'];
        }

        // check isset in session langauge locale
        if ( ! isset($session->data['language']))
        {
            $session->data['language'] = self::$_lang;
        }
        elseif ($session->data['language'] != self::$_lang)
        {
            $session->data['language'] = self::$_lang;
        }

        //  check isset in cookie language locale
        if ( ! isset($cookie['language']))
        {
            setcookie('language',self::$_lang, time() + 60 * 60 * 24 * 30, '/', $server['HTTP_HOST']);
        }
        elseif ($cookie['language'] != self::$_lang)
        {
            setcookie('language', self::$_lang, time() + 60 * 60 * 24 * 30, '/', $server['HTTP_HOST']);
        }

    }

    // --------------------------------------------------------------------

    /**
     * get translated string
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public static function get($key)
    {
        #$value = isset(self::$_dictionary[$key]) ? self::$_dictionary[$key] : $key;
        if(!isset(self::$_dictionary[$key]))
        {
            self::$_dictionary[$key] = self::_getFromSql($key, self::$_lang);
            if(self::$_dictionary[$key] === $key && self::$_lang != self::$_default)
                self::$_dictionary[$key] = self::_getFromSql($key, self::$_default);
        }
        return self::$_dictionary[$key];
    }

    // --------------------------------------------------------------------

    /**
     * get translated string from sql
     *
     * @access	protected
     * @param	string
     * @param   string
     * @return	string
     */
    private static function _getFromSql($key, $locale)
    {
        $result = self::$db->query("SELECT * FROM `" . DB_PREFIX . "translate`
                                        WHERE locale = '" . $locale . "'
                                        AND label = '" . $key . "'");
        if($result->count > 0)
            return $result->first['content'];
        else return $key;
    }

    // --------------------------------------------------------------------

    /**
     * get type language
     *
     * @access	public
     * @return	string
     */
    public static function get_lang()
    {
        return self::$_lang;
    }

    // --------------------------------------------------------------------

    /**
     * get name language
     *
     * @access	public
     * @return	string
     */
    public static function get_name()
    {
        return self::$_name;
    }

    // --------------------------------------------------------------------

    /**
     * get list language type => name
     *
     * @access	public
     * @return	array
     */
    public static function get_list_languges()
    {
        return self::$_list_languges;
    }

    // --------------------------------------------------------------------

    /**
     * get list language type => name
     *
     * @access	public
     * @param	array
     */
    protected static function _set_list_languges($query)
    {
        $list = array();

        foreach ($query->list as $result)
        {
            $list[$result['lang']] = $result['name'];
        }

        self::$_list_languges = $list;
    }
}


/* End of file mui.php */
/* Location: ./system/library/mui.php */