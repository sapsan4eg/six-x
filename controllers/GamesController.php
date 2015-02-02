<?php
class GamesController extends Controller 
{
	public function Index()
	{
		$this->join->model('Games/Main');
		$this->GamesMainModel->get_values();
		
		return $this->view->ViewResult();
	}
	public function Parser()
	{
		$this->join->model('Games/Main');
		$this->GamesMainModel->get_values();
		
		return $this->view->ViewResult();
	}
	public function GetTeams()
	{
		$this->join->model('Games/Main');
		$answer = array('answer' => $this->GamesMainModel->set_teams());		
		return $this->view->JsonResult($answer);
	}
	public function GetTeamsDefaults()
	{
		$this->join->model('Games/Main');
		$answer = $this->GamesMainModel->set_teams_defaults();	
		return $this->view->JsonResult($answer);
	}
	public function SetPlayers()
	{
		$this->join->model('Games/Main');
		$answer = array();
		if (isset($this->request->post['team']))
		{
			if(is_numeric($this->request->post['team']))
			{
				$answer = $this->GamesMainModel->set_players($this->request->post['team']);	
			}
			
		}
		return $this->view->JsonResult($answer);
	}
	public function GetListTeams()
	{
		$this->join->model('Games/Main');
		$answer = $this->GamesMainModel->get_list_teams();	
		return $this->view->JsonResult($answer);
	}
	public function SetMatches()
	{
		$this->join->model('Games/Main');
		$answer = array();
		for($i = 1998; $i < 2014; $i++)
		{
			$answer = $this->GamesMainModel->set_matches('2', $i);
		}
		return $this->view->JsonResult($answer);
	}
	public function SetMatchesDetails()
	{
		$this->join->model('Games/Main');
		$answer = array();
		//for($i = 1998; $i < 2014; $i++)
		//{
			$answer = $this->GamesMainModel->set_matches_details(4964); //4871
		//}
		return $this->view->JsonResult($answer);
	}
}

/* End of file GamesController.php */
/* Location: ./controllers/GamesController.php */