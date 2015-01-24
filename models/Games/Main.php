<?php
class Games_Main_model extends Object
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
		$this->view->links = $this->MainModel->home_links();
	}
}

/* End of file Main.php */
/* Location: ./models/Payment/Main.php */