<?php
class TestController extends Controller
{
	public function Index()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->links = array('mainlink' => array('link' => $this->router->Link('Index', 'Home'), 'title' => ''));	
		return $this->view->ViewResult();
	}
	public function Json()
	{
		$array = array('re' => array('test' =>'er'));
		return $this->view->JsonResult($array);
	}
	public function TestAutorize()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->links = array('mainlink' => array('link' => $this->router->Link('Index', 'Home'), 'title' => ''));	
		return $this->view->ViewResult('News', true, 'Home');	}
}

/* End of file TestController.php */
/* Location: ./controllers/TestController.php */