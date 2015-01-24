<?php
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
 * Validation Class
 *
 * @package		six-x
 * @subpackage	Libraries
 * @category	Validation
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Validation extends Object
{
	public $current_error = '';
	
	/**
	 * Constructor
	 */
	public function __construct($storage)
	{
		$this->_storage = new Storage();
		$this->db = $storage->get('db');
		$this->mui = $storage->get('mui');
		$this->mui->load('Validation/validation');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Verify the applicable data rules
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function is_valid ($data, $rules = '')
	{
		// Clear current error
		$this->current_error = '';
		
		// No data or incorrectly set the rules, then what are we doing here?
		if( ! is_string($data) OR ! is_string($rules))
		{
			$this->current_error = $this->mui->get('validation_no_data');
			return FALSE;
		}		
		
		// --------------------------------------------------------------------
		
		// Do not set the rules, so what's your problem.
		if(strlen($rules) == 0)
		{
			return TRUE;
		}
		
		// --------------------------------------------------------------------
		
		// This value must not be empty		
		if(strlen($data) == 0 && (strpos($data, 'required') !== FALSE OR strpos($data, 'isset') !== FALSE))
		{
			$this->current_error = $this->mui->get('validation_required');
			return FALSE;
		}
		
		// --------------------------------------------------------------------
		
		// Transform into an array of rules
		$rules = explode('|', $rules);
		
		// Cycle through each rule and run it
		foreach ($rules As $rule)
		{
			// Remove the spaces in the name rules
			$rule = trim($rule);
			
			// Strip the parameter (if exists) from the rule
			// Rules can contain a parameter: max_length[5]
			$param = FALSE;
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match))
			{
				$rule	= $match[1];
				$param	= $match[2];
			}
			
			// Call the function that corresponds to the rule
			if ( ! method_exists($this, $rule))
			{
				// If our own wrapper function doesn't exist we see if a native PHP function does.
				// Users can use any native PHP function call that has one param.
				if (function_exists($rule))
				{
					$result = $rule($data);
					if( ! is_bool($result))
					{
						$data = $result;
						continue;
					}
					if($result === FALSE)
					{
						$this->current_error = $this->mui->get('validation_native') . ' ' . $rule;
						return FALSE;
					}
				}
				else 
				{
					trigger_error('Error: could not find the function - ' . $rule . '() assigned in rules list!');
				}
				continue;
			}
			
			$result = $this->$rule($data, $param);
			if( ! is_bool($result))
			{
				$data = $result;
				continue;
			}
			if($result === FALSE)
			{
				$this->current_error = str_replace('{param}', $param, $this->mui->get('validation_' . $rule));
				return FALSE;
			}
		}
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Performs a Regular Expression match test.
	 *
	 * @access	public
	 * @param	string
	 * @param	regex
	 * @return	bool
	 */
	public function regex_match($str, $regex)
	{
		if ( ! preg_match($regex, $str))
		{
			return FALSE;
		}

		return  TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function matches($str, $data)
	{
		return ($str !== $field) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Emails
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_space($str)
	{
		
		return  strlen($str) > 0 ? (( ! preg_match("/^([a-z ])+$/i", $str)) ? FALSE : TRUE) : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Greather than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function greater_than($str, $min)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str > $min;
	}

	// --------------------------------------------------------------------

	/**
	 * Less than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function less_than($str, $max)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str < $max;
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}

		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Checking dates in format MM / YY
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function month_year($str)
	{
		$str = str_replace(' ', '', $str);
		$date = explode('/', $str);
		if(count($date) == 2 && $this->is_natural($date[0]) && $this->is_natural($date[1]))
		{
			if(strlen($date[1]) == 2)
			{
				$current_cent = date("Y");
				$current_cent = substr($current_cent, 0, 2);
				$date[1] = $current_cent . $date[1];
			}
		
			return checkdate($date[0], 1, $date[1]);
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Checking dates in format MM / YY greater than now
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function dates_greater_now($str)
	{
		if($this->month_year($str))
		{
			$str = str_replace(' ', '', $str);
			$date = explode('/', $str);
			$year = date("Y");
			$month = date("m");
			if(strlen($date[1]) == 2)
			{
				$current_cent = substr($year, 0, 2);
				$date[1] = $current_cent . $date[1];
			}
			if(($year == $date[1] && $month <= $date[0]) OR $year < $date[1])
			{
				return TRUE;
			}
		}		
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Checking card number
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function card_number($str)
	{
		$str = $this->clear_space($str);
		
		if($this->is_natural_no_zero($str))
		{
			$cards = $this->_cards();
			
			foreach($cards As $card)
			{				
				if($this->regex_match($str, $card['mask']))
				{
					if((is_array($card['length']) ? in_array(strlen($str), $card['length']) : $card['length'] == strlen($str)) && ($card['luhn'] === FALSE OR $this->luhn_test($str)))
					{
						return TRUE;
					}			
				}
			}
			
			return FALSE;
		}
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * list of cards
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function _cards()
	{
		$cards = array();
		
		$cards[] = array('name' => 'visaelectron', 'mask' => '/^4(026|17500|405|508|844|91[37])/', 'length' => 16, 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'maestro', 'mask' => '/^(5(018|0[23]|[68])|6(39|7))/', 'length' => array(12, 13, 14, 15, 16, 17, 18, 19), 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'forbrugsforeningen', 'mask' => '/^600/', 'length' => 16, 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'dankort', 'mask' => '/^5019/', 'length' => 16, 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'visa', 'mask' => '/^4/', 'length' => array(13, 16), 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'mastercard', 'mask' => '/^5[0-5]/', 'length' => 16, 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'amex', 'mask' => '/^3[47]/', 'length' => 15, 'cvc_length' =>  array(3, 4), 'luhn' => TRUE);
		$cards[] = array('name' => 'dinersclub', 'mask' => '/^3[0689]/', 'length' => 14, 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'discover', 'mask' => '/^6([045]|22)/', 'length' => 16, 'cvc_length' => 3, 'luhn' => TRUE);
		$cards[] = array('name' => 'unionpay', 'mask' => '/^(62|88)/', 'length' => array(16, 17, 18, 19), 'cvc_length' => 3, 'luhn' => FALSE);
		$cards[] = array('name' => 'jcb', 'mask' => '/^35/', 'length' => 16, 'cvc_length' => 3, 'luhn' => TRUE);
		
		return $cards;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Check luhn
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function luhn_test($str) 
	{
	    $len = strlen($str);
		$sum = 0;
		$ord = TRUE;
	    for ($i = $len - 1; $i >= 0; $i--) 
	    {
	    	$digit = (int)$len[$i];
	    	if (($ord = !$ord)) 
	    	{
		        $digit *= 2;
		    }
	      	if ($digit > 9) 
	      	{
	        	$digit -= 9;
	      	}
			$sum += $digit;
	    }       
	    return $sum % 10 === 0;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function is_unique($str, $where)
	{
		list($table, $field) = explode('.', $where);
		$query = $this->db->query("SELECT * FROM `" . $table .  "` WHERE `" . $field . "` = '" . $this->db->escape($str) . "'");

		return $query->count === 0;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Remove all spaces in string
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function clear_space($str)
	{		

		return str_replace(' ', '', $str);
	}	
}

/* End of file validation.php */
/* Location: ./system/library/validation.php */