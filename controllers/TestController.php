<?php
class TestController extends Controller
{
	public function Index()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->links = array('mainlink' => $this->router->Link('Index', 'Home'));
		return $this->view->ViewResult();
	}
	public function Json()
	{
        $array = array('server' => array('shema' =>'http', 'port' => '80',
            'url' => 'energouchet.com', 'action' => 'Show', 'controller' => 'Home'
        ), 'id' => '1'
        );
        if($_SERVER['REQUEST_METHOD'] == "POST")
        {
            $array["request"] = "POST";
            $array["token"] = $_POST["token"];
            $array["from_token"] = json_decode( base64_decode( $_POST['token']));
        }
        $array["from_token"] = json_decode(base64_decode("eyJpZCI6IjEiLCJzaWduIjoi77+977+9Qjjvv73vv70j77+9XHLvv71Q77+9b3Xvv73vv70ifQ=="));
		return $this->view->JsonResult($array);
	}
	public function TestAutorize()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->view->links = array('mainlink' => $this->router->Link('Index', 'Home'));
		return $this->view->ViewResult('News', true, 'Home');
	}
}

/* End of file TestController.php */
/* Location: ./controllers/TestController.php */