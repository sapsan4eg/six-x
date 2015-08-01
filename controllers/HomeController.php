<?php
class HomeController extends Controller 
{
	public function Index()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->links = $this->MainModel->home_links();
		$this->join->model('Story/Manipulation');
        $search = ['{Redirected}', '{File}', '{Json}','{Part}','{Aut}', '{Log}'];
		$story = $this->StoryManipulationModel->show_story(1, Six_x\Mui::get_lang());

		if(count($story) > 0)
		{
			$this->view->title = urldecode($story['meta_title']);
			$this->view->bodytext = str_replace($search, $this->MainModel->home_links(), htmlspecialchars_decode(urldecode($story['texts'])));
			$this->view->description = urldecode($story['meta_description']);
			$this->view->keywords = urldecode($story['meta_keyword']);
		}
		return $this->view->ViewResult();
	}

	public function Test()
	{
		$this->view->RedirectToAction('Index', 'Test');
	
	}

	public function News()
	{
		$this->view->FileResult(DIR_IMAGE . 'test.jpg', 'newname');

	}

	public function Partial()
	{
		$this->join->model('Main');
		$this->view->links = $this->MainModel->home_links();
        $this->join->model('Main');
        $this->MainModel->get_main_values();
        $this->view->links = $this->MainModel->home_links();
        $this->join->model('Story/Manipulation');
        $story = $this->StoryManipulationModel->show_story(1, Six_x\Mui::get_lang());
        if(count($story) > 0)
        {
            $this->view->title = urldecode($story['meta_title']);
            $this->view->bodytext = htmlspecialchars_decode(urldecode($story['texts']));
            $this->view->description = urldecode($story['meta_description']);
            $this->view->keywords = urldecode($story['meta_keyword']);
        }
		return $this->view->PartialViewResult('Index');
	}

	public function ChangeLanguage()
	{
		$array = array('answer' => 'cannot');
		if(isset($this->request->post['language']))
		{
			$list = Six_x\Mui::get_list_languges();
			if(isset($list[$this->request->post['language']]))
			{
				$this->session->data['language'] = $this->request->post['language'];
				$array = array('answer' => 'success');
			}
		}
		return $this->view->JsonResult($array);
	}

}

/* End of file HomeController.php */
/* Location: ./controllers/HomeController.php */