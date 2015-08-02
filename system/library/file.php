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
class File extends Arrays
{
    protected static $_path = '/';
    protected $_real_array = [];
    protected $_safty = ['image/jpeg', 'image/png', 'application/pdf', 'text/plain'];
    protected $_size = 2097151;
    protected $_error = 0;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param	string
     */
    public function __construct($path = NULL)
    {
        if( ! empty($path))
        {
            $this->path($path);
            $this->_array();
        }
    }

    // --------------------------------------------------------------------

    /**
     * sets the path
     *
     * @param   string
     * @return	mixed
     */
    public function path($path = NULL)
    {
        if ($path === NULL) return $this::$_path;
        $this::$_path = is_dir(realpath((string)$path)) ? realpath((string)$path) : '/';
        $this::$_path .= strrpos($this::$_path, '/') === strlen($this::$_path) -1 ? '' : '/';
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * list files and directories inside the specified path
     *
     * @param   string
     * @return	bool
     */
    protected function _array()
    {
        $this->_real_array = $this->_array = scandir($this::$_path);
    }

    // --------------------------------------------------------------------

    /**
     * assigns the local variable array of original
     *
     * @param   string
     * @return	object
     */
    public function original()
    {
        $this->_array = $this->_real_array;
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * sets the path and creates an array of files
     *
     * @param   string
     * @return	object
     */
    public function files($path = NULL)
    {
        $this->path($path);
        $this->_array();
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * get info from files
     *
     * @param   string
     * @return	object
     */
    public function filesInfo()
    {
       $this->_array = array_map(['Six_x\File', 'info'], (array)$this->_array);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * get info from file
     *
     * @param   string
     * @return	array
     */
    public static function info($string)
    {
        if (is_array($string)) return array_map(['Six_x\File', 'info'], $string);

        $string = \Six_x\File::$_path . $string;

        if (file_exists($string)) {
            $info = new \SplFileInfo($string);
            return ['name' => $info->getBasename(), 'type' => empty($info->getExtension()) ? filetype($string) : $info->getExtension(), 'size' =>  $info->getSize(), 'last' => $info->getCTime()];
        }

        return ['name' => basename($string), 'type' => 'not understand', 'size' => 'not understand', 'last' => 1];
    }

    // --------------------------------------------------------------------

    /**
     * upload file
     *
     * @param   string
     * @return	mixed
     */
    public function upload($filename = '', $newname = '')
    {
        $this->_error = $this->validate($filename);
        if($this->_error === 0)
        {
            $name = ( ! empty($newname) ? $newname : str_replace('.', '', microtime (true)));
            $spl = new \SplFileInfo($name);
            $name = $spl->getBasename('.' . $spl->getExtension()) . '.' . (new \SplFileInfo($_FILES[$filename]['name']))->getExtension();

            if(move_uploaded_file($_FILES[$filename]['tmp_name'], $this::$_path . $name))
            {
                return $this::$_path . $name;
            } else $this->_error = 12;
        }
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * validate upload file
     *
     * @param   string
     * @return	int
     */
    protected function validate($filename = '')
    {
        if( ! $this->uploaded($filename)) return 9;
        if($_FILES[$filename]['error'] > 0) return $_FILES[$filename]['error'];
        if($_FILES[$filename]['size'] > $this->_size OR $_FILES[$filename]['size'] == 0) return 10;
        if( ! count(array_keys($this->_safty, $_FILES[$filename]['type'])) > 0) return 11;

        return 0;
    }

    // --------------------------------------------------------------------

    /**
     * check is uploaded file
     *
     * @param   string
     * @return	bool
     */
    public function uploaded($filname = '')
    {
        return ! empty($_FILES[$filname]);
    }

    // --------------------------------------------------------------------

    /**
     * check is uploaded file
     *
     * @param   string
     * @return	bool
     */
    public function getError()
    {
        return $this->_error;
    }

    // --------------------------------------------------------------------

    /**
     * check exist file in directory
     *
     * @param   string
     * @return	bool
     */
    public function have($name = '')
    {
        return empty($name) ? FALSE : file_exists($this::$_path . (new \SplFileInfo($name))->getBasename());
    }

    // --------------------------------------------------------------------

    /**
     * check is writable file
     *
     * @param   string
     * @return	bool
     */
    public function writable($name = '')
    {
        return empty($name) ? FALSE : is_writable($this::$_path . (new \SplFileInfo($name))->getBasename());
    }

    // --------------------------------------------------------------------

    /**
     * delete file from directory
     *
     * @param   string
     * @return	bool
     */
    public function delete($name = '')
    {
        if($this->have($name) AND is_dir($this::$_path . (new \SplFileInfo($name))->getBasename()))
        {
            return $this->writable($name) ? rmdir($this::$_path . (new \SplFileInfo($name))->getBasename()) : FALSE;
        }
        return $this->have($name) ?
            ($this->writable($name) ? unlink($this::$_path . (new \SplFileInfo($name))->getBasename()) : FALSE)
            : TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * rename file in directory
     *
     * @param   string
     * @param   string
     * @return	bool
     */
    public function rename ($name = '', $newname = '')
    {
        if ($this->writable($name) AND ( ! $this->have($newname) OR  $this->writable($newname)))
        {
            return rename($this::$_path . (new \SplFileInfo($name))->getBasename(), $this::$_path . (new \SplFileInfo($newname))->getBasename());
        }
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * copy file in directory
     *
     * @param   string
     * @param   string
     * @return	bool
     */
    public function copy($name = '', $newname = '')
    {
        if ( ! empty($name) AND ! empty($newname) AND $this->writable($name) AND (
                is_writable(realpath($newname)) OR
                ! file_exists(realpath($newname)) AND
                is_writable(realpath(dirname($newname)))))
        {
            return copy($this::$_path . (new \SplFileInfo($name))->getBasename(), realpath(dirname($newname)) . '/' . basename($newname));
        }
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * remove file in directory
     *
     * @param   string
     * @param   string
     * @return	bool
     */
    public function remove($name = '', $newname = '')
    {
        if ( ! empty($name) AND ! empty($newname) AND $this->writable($name) AND (
                is_writable(realpath($newname)) OR
                ! file_exists(realpath($newname)) AND
                is_writable(realpath(dirname($newname)))))
        {
            return rename($this::$_path . (new \SplFileInfo($name))->getBasename(), realpath(dirname($newname)) . '/' . basename($newname));
        }
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * make new directory
     *
     * @param   string
     * @return	bool
     */
    public function newdir($name = '')
    {
        if(! empty($name) AND ! file_exists($this::$_path . explode('.', basename($name))[0]))
        {
            return mkdir($this::$_path . explode('.', basename($name))[0]);
        }
        return FALSE;
    }
}

/* End of file file.php */
/* Location: ./system/library/file.php */