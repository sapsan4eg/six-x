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
 * Mysqli Database Adapter Class
 *
 * @package		six-x
 * @subpackage	database
 * @category	Database
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/guide/database/
 */
final class Mysqli_x {

	protected $sql;

	/**
	 * Constructor
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 */

	public function __construct($hostname, $username, $password, $database)
	{
		// Check is loaded extension mysqli

		if (extension_loaded('mysqli'))
		{
			// Check is exist class mysqli

			if(class_exists("mysqli"))
			{
				// Save link to connection
				$this->sql = new mysqli($hostname, $username, $password, $database);

				if ($this->sql->connect_errno)
				{
					trigger_error("Cannot connect to MySQL server: " . $this->sql->connect_error);
				}
				else
				{
					$this->sql->set_charset("utf8");
					$this->sql->query("SET NAMES 'utf8'");
					$this->sql->query("SET CHARACTER SET utf8");
					$this->sql->query("SET CHARACTER_SET_CONNECTION=utf8");
					$this->sql->query("SET SQL_MODE = ''");
				}
			}
			else
			{
				trigger_error('Error: Could not find mysqli class');
			}
		}
		else
		{
			trigger_error('Error: Could not load mysqli extension');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Execute query in database
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function query($query, $writeble = FALSE)
	{
        if($writeble === FALSE && $this->_is_writeble($query))
        {
            throw new ErrorException('Error: corrupted sql. Error No: 666' . $query);
        }
		$result = $this->sql->query($query);

		if ( ! $this->sql->errno)
		{
			if (isset($result->num_rows))
			{
				$data = array();

				while ($row = $result->fetch_assoc())
				{
					$data[] = $row;
				}

				$return = new stdClass();
				$return->count = $result->num_rows;
				$return->first = isset($data[0]) ? $data[0] : array();
				$return->list = $data;
				$return->last = isset($data[$result->num_rows -1]) ? $data[$result->num_rows -1] : array();

				unset($data);

				$result->close();

				return $return;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			throw new ErrorException('Error: ' . $this->sql->error . '<br />Error No: ' . $this->sql->errno . '<br />' . $query);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Escape
	 *
	 * @param	string
	 * @return	string
	 */
	public function escape($value)
	{
		return $this->sql->real_escape_string($value);
	}

	// --------------------------------------------------------------------

	/**
	 * Count affected
	 *
	 * @return	int
	 */
	public function countAffected()
	{
		return $this->sql->affected_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Get last inserted id
	 *
	 * @return	int
	 */
	public function getLastId()
	{
		return $this->sql->insert_id;
	}

	// --------------------------------------------------------------------

	/**
	 * Close link to db
	 */
	public function __destruct()
	{
		$this->sql->close();
	}

    // --------------------------------------------------------------------

    /**
     * Determines if a query is a "write" type.
     *
     * @param	string
     * @return	bool
     */
    protected function _is_writeble ($sql)
    {
        $array = ['SET','INSERT','UPDATE','DELETE','REPLACE','CREATE','DROP','TRUNCATE','LOAD','COPY','ALTER','RENAME','GRANT','REVOKE','LOCK','UNLOCK','REINDEX'];
        foreach($array As $value)
        {
            if(strpos($sql, $value) !== FALSE)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    // --------------------------------------------------------------------
}

/* End of file mysqli.php */
/* Location: ./system/database/mysqli.php */