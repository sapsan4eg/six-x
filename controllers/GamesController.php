<?php
class GamesController extends Controller 
{
	public function Index()
	{
		$this->join->model('Games/Main');
		$this->GamesMainModel->get_values();
		
		return $this->view->ViewResult();
	}
}

/* End of file GamesController.php */
/* Location: ./controllers/GamesController.php */