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
		$data['message'] = array("danger", $data['code'], _('error_not_found'));
		$data['title'] = "Error: " .$data['code'];
		$this->view->set(array_merge($this->view->get(), $data));	
		$this->view->links = array('mainlink' => $this->router->Link('Index', 'Home'));
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		return $this->view->NotFoundResult('error');
	}
	public function ErrorLog()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
        $path = defined('DIR_ERRORS') ? DIR_ERRORS : DIR_SYSTEM . 'logs/';
        $file = new Six_x\File($path);
        $array = $file->toArray();
        arsort($array);
        $newarray = [];
        foreach($array As $value)
        {
            if(strpos($value, '.log') !== FALSE)
                $newarray[] = substr($value, 0, strpos($value, '.log'));
        }
        $this->view->listlogs = $newarray;

        if(isset($this->request->get['logfile']) && validateDate($this->request->get['logfile'], "Y-m-d"))
            $this->view->logfile = $this->request->get['logfile'];
        else
            $this->view->logfile = count($newarray) > 0 ? $newarray[0] : date("Y-m-d");

		$this->view->log	= Log::GetText($this->view->logfile);
		$this->view->links	= array('mainlink' => $this->router->Link('Index', 'Home'),
									'clearlog' => $this->router->Link('ErrorLogClear', 'Error', array("logfile" => $this->view->logfile))
		);	
		return $this->view->ViewResult('Log');
	}
	public function ErrorLogClear()
	{
		if(isset($this->request->post['clear']) && isset($this->request->get['logfile']) && validateDate($this->request->get['logfile'], "Y-m-d"))
		{
			return $this->view->JsonResult(array('answer' => Log::ClearLog($this->request->get['logfile']) ? 'success' : 'fail'));
		}
		return $this->view->JsonResult(array('answer' => 'fail'));
	}
}

/* End of file ErrorController.php */
/* Location: ./controllers/ErrorController.php */