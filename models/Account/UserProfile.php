<?php
class Account_UserProfile_model extends Object
{	public function get_user_info()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();		$return = array();		if($this->autorization != null)
		{        	$return['Name'] = $this->autorization->Identity['Name'];		}
		return	$return;	}}

/* End of file UserProfile.php */
/* Location: ./models/Account/UserProfile.php */