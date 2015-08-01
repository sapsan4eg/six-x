<?php
class Payment_Main_model extends Object
{
	/**
	 * gets the name of the variables
	 *
	 * @access	public
	 * @return	void
	 */
	public function get_values()
	{
		// Load main model
		$this->join->model('Main');
		
		// get main values from main model
		$this->MainModel->get_main_values();
		
		// insert values translated text
		$data['personal_card']		= _('text_personal_card');
		$data['number']				= _('text_card_number');
		$data['expiry']				= _('text_expiry');
		$data['cvc']				= _('text_cvc');
		$data['card_holder']		= _('text_card_holder_name');
		$data['expiry_holder']		= _('expiry_holder');
		$data['cvc_holder']			= _('cvc_holder');
		$data['button_submit']		= _('button_submit');
		$data['help_cvc_title']		= _('help_cvc_title');
		$data['help_cvc_content']	= _('help_cvc_content');
		
		// error translated text
		$data['error_main']			= _('error_main');
		$data['error_number']		= _('error_number');
		$data['error_expiry']		= _('error_expiry');
		$data['error_cvc']			= _('error_cvc');
		$data['error_cardholder']	= _('error_cardholder');
		
		$this->view->set(array_merge($this->view->get(), $data));
	}

	// --------------------------------------------------------------------

	/**
	 * check the data
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function validate_post_data ($data) 
	{
		// list of errors
		$errors	= array();
		
		$type_answer	= "success";
		$answer			= "";
		
		// load library validation
		$this->join->library('validation');		
		
		// Check number data
		if( ! $this->validation->is_valid(isset($data['cc-number']) ? $data['cc-number'] : NULL, 'clear_space|required|card_number|is_unique[payment.card_number]'))
		{
			$errors[] = _('error_number') . ' (' . $this->validation->current_error . ')';
		}
		
		// Check expire data$data['cc-exp']
		if( ! $this->validation->is_valid(isset($data['cc-exp']) ? $data['cc-exp'] : NULL, 'required|dates_greater_now'))
		{
			$errors[] = _('error_expiry') . ' (' . $this->validation->current_error . ')';
		}
		
		// Check cvc data
		if( ! $this->validation->is_valid(isset($data['cc-cvc']) ? $data['cc-cvc'] : NULL, 'required|min_length[3]|max_length[4]'))
		{
			$errors[] = _('error_cvc') . ' (' . $this->validation->current_error . ')';
		}
		
		// Check card holder name
		if( ! $this->validation->is_valid(isset($data['holder']) ? $data['holder'] : NULL, 'alpha_space'))
		{
			$errors[] = _('error_cardholder') . ' (' . $this->validation->current_error . ')';
		}
		if(count($errors) == 0)
		{
			$exp = explode('/', $this->validation->clear_space($data['cc-exp']));
			
			// try insert values into database
			try
			{
				$result = $this->db->query("INSERT INTO `payment` (`card_number`, `expire_month`, `expire_year`, `cvc`, `card_holder`, `user_id`, `when_add`) 
              								VALUES('" . $this->validation->clear_space($data['cc-number']) . "', 
              								" . $exp[0] . ", " . $exp[1] . ", " . $data['cc-cvc'] . ", '" . $data['holder'] . "', 
              								" . $this->autorization->Identity['id'] . ", '" . date('Y-m-d H:i:s') . "')");
				$answer = array('number' => $data['cc-number'], 'expire' => $data['cc-exp'], 'cvc' => $data['cc-cvc'], 'holder' => $data['holder']);
			} 
			catch(Exception $e)
			{
				$errors[] = _('error_upload_999') . ' ' . $e->getCode();
			}
		}
		if(count($errors) > 0)
		{
			// Create error description
			$answer = $this->_create_error($errors);
			$type_answer = "warning";
		}

		// Return answer
		$return = array(array($type_answer, _($type_answer),  $answer), $data);
		return $return;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Create error html
	 *
	 * @access	protected
	 * @param	array
	 * @return	string
	 */
	protected function _create_error($errors)
	{
		// string of numbered list
		$errorlist = '<ol>';
		
		// loop through all the errors
		foreach($errors As $err)
		{
			$errorlist .= '<li>' . $err . '</li>';
		}		
		$errorlist .= '</ol>';
		
		// error text to send browser
		$error = _('error_main') . " <a href='#' id='button_details'>" . _('button_details') . " ...</a>
		<script>
			$(function(){
				$('#button_details').popover({trigger : 'focus', title : '" . _('error_main') .
				"', container : 'body', html : true, content : '" . $errorlist . "'}).click(function(e){e.preventDefault();});
			});
		</script>
		";
		
		return $error;
	}
}

/* End of file Main.php */
/* Location: ./models/Payment/Main.php */