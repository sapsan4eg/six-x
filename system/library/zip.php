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
 * @subpackage	zip
 * @category	Zip
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/guide/zip/
 */
class Zip {

    /**
     * Zip data in string
     *
     * @var string
     */
    public $data = '';

    /**
     * Zip data for a directory in string
     *
     * @var string
     */
    public $dir = '';

    /**
     * Number of files/folder in zip file
     *
     * @var int
     */
    public $count = 0;

    /**
     * relative offset of local header
     *
     * @var int
     */
    public $lendata = 0;

    /**
     * The level of compression
     *
     * Ranges from 0 to 9, with 9 being the highest level.
     *
     * @var	int
     */
    public $level = 2;

    /**
     * The main path
     *
     * @var	int
     */
    public $path = '';

    // --------------------------------------------------------------------

    /**
     * Add Data to Zip
     *
     * Lets you add files to the archive. If the path is included
     * in the filename it will be placed within a directory. Make
     * sure you use add_dir() first to create the folder.
     *
     * @param	mixed	$file    	A single realfile
     * @param	string	$path		Single filepath
     * @return	void
     */
    public function add_data($file, $path = "", $byte_array = FALSE)
    {
        $name = "";
        $data = "";
        $mtime = [];

        if($byte_array == FALSE)
        {
            if(is_array($file))
            {
                foreach($file As $f)
                {
                    $this->add_data($f, $path);
                }
            }
            else
            {
                if (file_exists($file))
                {
                    if(is_dir($file) === FALSE && FALSE !== ($data = file_get_contents($file)))
                    {
                        $mtime = getdate(filemtime($file));
                        $name = $this->_name($file, $path);
                    } else return $this;
                } else return $this;
            }

        }
        elseif(is_array($file) && ! empty($file['data']) && ! empty($file['name']))
        {
            $name = $this->_name($file['name'], $path);
            $mtime = getdate(time());
            $data = $file['data'];
        }
        else return $this;

        $mtime = ['file_mtime' => ($mtime['hours'] << 11) + ($mtime['minutes'] << 5) + $mtime['seconds'] / 2,
            'file_mdate' => (($mtime['year'] - 1980) << 9) + ($mtime['mon'] << 5) + $mtime['mday']];

        $this->_add_data($data, $name, $mtime);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Add Data to Zip
     *
     * @param	string	$data	string bytes
     * @param	string	$name	the data to be encoded
     * @param	int	$mtime
     * @return	void
     */
    protected function _add_data($data, $name, $mtime)
    {
        $uncompressed_size = strlen($data);
        $crc32  = crc32($data);
        $gzdata = substr(gzcompress($data, $this->level), 2, -4);
        $compressed_size = strlen($gzdata);
        $this->data .=
                "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00"
            .pack('v', $mtime['file_mtime'])
            .pack('v', $mtime['file_mdate'])
            .pack('V', $crc32)
            .pack('V', $compressed_size)
            .pack('V', $uncompressed_size)
            .pack('v', strlen($name)) // length of filename
            .pack('v', 0) // extra field length
            .$name
            .$gzdata // "file data" segment
            .pack('V', $crc32)
            .pack('V', $compressed_size)
            .pack('V', $uncompressed_size);

        $this->dir .=
            "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00"
            .pack('v', $mtime['file_mtime'])
            .pack('v', $mtime['file_mdate'])
            .pack('V', $crc32)
            .pack('V', $compressed_size)
            .pack('V', $uncompressed_size)
            .pack('v', strlen($name)) // length of filename
            .pack('v', 0) // extra field length
            .pack('v', 0) // file comment length
            .pack('v', 0) // disk number start
            .pack('v', 0) // internal file attributes
            .pack('V', 32) // external file attributes - 'archive' bit set
            .pack('V', $this->lendata) // relative offset of local header
            .$name;

        $this->lendata = strlen($this->data);
        $this->count++;
    }

    // --------------------------------------------------------------------

    /**
     * Get the Zip file
     *
     * @return	string	(binary encoded)
     */
    protected function _name($name, $path)
    {
        $name = str_replace('\\', '/', $name);

        if(strrpos($name, '/') !== FALSE)
        {
            $name = substr($name, strrpos($name, '/') + 1);
        }

        if(strlen($path) > 0)
        {
            $name = (strrpos($path, '/') == (strlen($path) - 1) ? ($path . "") : ($path . "/")) . $name;
        }
        elseif(strlen($this->path) > 0)
        {
            $name = (strrpos($this->path, '/') == (strlen($this->path) - 1) ? ($this->path . "") : ($this->path . "/")) . $name;
        }

        return $name;
    }

    // --------------------------------------------------------------------

    /**
     * Get the Zip file
     *
     * @return	string	(binary encoded)
     */
    public function get_zip()
    {
        // Is there any data to return?
        if ($this->count === 0)
        {
            return FALSE;
        }

        return $this->data
        .$this->dir . "\x50\x4b\x05\x06\x00\x00\x00\x00"
        .pack('v', $this->count) // total # of entries "on this disk"
        .pack('v', $this->count) // total # of entries overall
        .pack('V', strlen($this->dir)) // size of central dir
        .pack('V', strlen($this->data)) // offset to start of central dir
        ."\x00\x00"; // .zip file comment length
    }

}

/* End of file zip.php */
/* Location: ./system/library/zip.php */