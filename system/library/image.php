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
 * File Class
 *
 * primitive functions with files
 *
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Image
{
    protected $_image;
    protected $_types = ['jpg', 'gif', 'png', 'jpeg'];

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param	string
     */
    public function __construct($name = NULL)
    {
        ! empty($name) ? $this->take($name) : NULL;
    }

    // --------------------------------------------------------------------

    /**
     * check is image file
     *
     * @param   string
     * @return	bool
     */
    protected function _have($name)
    {
        if( ! empty($name) AND
            file_exists(realpath($name)) AND
            count(array_keys($this->_types, strtolower((new \SplFileInfo($name))->getExtension()))) > 0)
        {
            return TRUE;
        }  else trigger_error('Image class exepted real image');
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * take image from file
     *
     * @param   string
     * @return	bool
     */
    public function take($name)
    {
        if($this->_have($name))
        {
            $type = (new \SplFileInfo($name))->getExtension();
            $keys = array_keys($this->_types, $type);
            if(count($keys) > 0)
            {
                $func = 'imagecreatefrom' . str_replace('jpg', 'jpeg', $this->_types[$keys[0]]);
                if(function_exists($func))
                {
                    $this->_image = [
                        'path'  => realpath($name),
                        'info'  => getimagesize($name),
                        'type'  => (new \SplFileInfo($name))->getExtension(),
                        'image' => $func(realpath($name))
                    ];
                } else trigger_error('Function ' . $func . ' not exist');
            } else trigger_error('Not supported image format');
        }
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * save image
     *
     * @param   string
     * @return	bool
     */
    public function save($name)
    {
        if( ! empty($name) AND ! empty($this->_image) AND is_resource($this->_image['image']))
        {
            $real = realpath(dirname($name)) . '/' . explode('.', basename($name))[0] . '.' . $this->_image['type'];

            if( is_writable($real) OR
                ! file_exists($real) AND
                is_writable(realpath(dirname($name))))
            {
                $func = 'image' . str_replace('jpg', 'jpeg', $this->_image['type']);
                $func($this->_image['image'], $real);
                imagedestroy($this->_image['image']);
                $this->_image = [];
                return TRUE;
            }
        }
        return FALSE;
    }
}

/* End of file image.php */
/* Location: ./system/library/image.php */