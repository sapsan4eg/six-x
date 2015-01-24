<?php
class ErrorController extends Controller
{
	public function Index()
	{
		$this->view->RedirectToAction('not_found');
	}
	public function not_found()
	{
		$data['code'] = '404';
		$data['message'] = array("danger", $data['code'], $this->mui->get('error_not_found'));
		$data['title'] = "Error: " .$data['code'];
		$this->view->set(array_merge($this->view->get(), $data));	
		$this->view->links = array('mainlink' => array('link' => $this->router->Link('Index', 'Home'), 'title' => ''));	
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		return $this->view->NotFoundResult('error');
	}
	public function ErrorLog()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->log = Log::GetText();
		$this->view->links = array('mainlink' => array('link' => $this->router->Link('Index', 'Home'), 'title' => ''));	
		return $this->view->ViewResult('Log');
	}
}

/* End of file ErrorController.php */
/* Location: ./controllers/ErrorController.php */