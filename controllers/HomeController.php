<?php
class HomeController extends Controller 
{
	public function Index()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->links = $this->MainModel->home_links();
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
		return $this->view->PartialViewResult('Index');
	}

	public function ChangeLanguage()
	{
		$array = array('answer' => 'cannot');
		if(isset($this->request->post['language']))
		{
			$list = $this->mui->get_list_languges();
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