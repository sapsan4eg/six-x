<?php
class PaymentController extends Controller
{
	public function Index()
	{		
		$this->join->model('Payment/Main');
		$this->PaymentMainModel->get_values();
		$this->view->links = array('mainlink' => array('link' => $this->router->Link('Index', 'Home'), 'title' => $this->mui->get('text_home')));
		if($this->request->server['REQUEST_METHOD'] == 'POST')
		{
			$answer = $this->PaymentMainModel->validate_post_data($this->request->post);
			if($answer[0][0] == 'success')
			{
				$this->session->data['message'] = $answer[0];
				$this->view->RedirectToAction('Success');
			}
			$this->view->message		= $answer[0];
			$this->view->values			= $answer[1];
		}
		return $this->view->ViewResult();
	}

	public function Success()
	{
		if($this->view->has('message'))
		{
			if($this->view->message[0] == 'success')
			{
				$this->join->model('Payment/Main');
				$this->PaymentMainModel->get_values();
				$this->view->linkBuck = array('link' => $this->router->link('Index'), 'title' => $this->mui->get('button_add_cart'));
				$this->view->card_data = $this->view->message[2];
				$this->view->message = null;
				$this->view->answer_title = $this->mui->get('success_card_add');
				return $this->view->ViewResult();
			}
		}
		$this->view->RedirectToAction($this->router->route['default_action'], $this->router->route['default_controller']);
	}
}

/* End of file PaymentController.php */
/* Location: ./controllers/PaymentController.php */