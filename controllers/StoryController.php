<?php
class StoryController extends Controller
{
	public function Index()
	{
		$this->join->model('Story/Manipulation');

		if($this->request->server['REQUEST_METHOD'] == 'POST')
		{
			$array = $this->StoryManipulationModel->delete_storys($this->request->post['story']);
			$this->view->set(array_merge($this->view->get(), $array));
		}
		
		$array = $this->StoryManipulationModel->get_storys();
		$this->view->set(array_merge($this->view->get(), $array));
		return $this->view->ViewResult();
	}
	public function Edit()
	{
		if(isset($this->request->get['story']) AND is_numeric($this->request->get['story']))
		{
			$this->join->model('Story/Manipulation');
			$array = FALSE;
			
			if($this->request->server['REQUEST_METHOD'] == 'POST')
			{
				$array = $this->StoryManipulationModel->story($this->request->get['story'], $this->request->post);
			}
			else 
			{
				$array = $this->StoryManipulationModel->story($this->request->get['story']);
			}
			if($array != FALSE)
			{
				$array['link_form'] = $this->router->Link('Edit','Story', array('story' => $this->request->get['story']));
				$this->view->set(array_merge($this->view->get(), $array));
				return $this->view->ViewResult('manipulate');
			}
		}
		return $this->view->RedirectToAction('Index');
	}
	public function Create()
	{
		$this->join->model('Story/Manipulation');
			
		if($this->request->server['REQUEST_METHOD'] == 'POST')
		{
			$array = $this->StoryManipulationModel->create($this->request->post);
		}
		else
		{
			$array = $this->StoryManipulationModel->create();
		}
		$this->view->set(array_merge($this->view->get(), $array));
		return $this->view->ViewResult('manipulate');
	}
}
/* End of file StoryController.php */
/* Location: ./controllers/StoryController.php */