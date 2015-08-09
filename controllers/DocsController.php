<?php
class DocsController extends Controller {
    public function Index()
    {
        $this->join->model('Docs/Builder');
        $step = 2;
        $page = ( ! empty($this->request->get['page']) ? (int) $this->request->get['page'] : 0);
        $list = $this->DocsBuilderModel->getList($page * $step, $step);
        $this->view->groups_docs = $list[1];
        $this->view->pages = ['count' => $list[0], 'current' => $page, 'step' => $step, 'link' => $this->router->link('Index')];
        $this->view->filelink = $this->router->link('GetFile');
        $this->view->ziplink = $this->router->link('GetArhiv');

        return $this->view->ViewResult();
    }
    public function GetFile()
    {
        if( ! empty($this->request->get['file']) && ! empty($this->request->get['group']))
        {
            $this->join->model('Docs/Builder');
            $doc = $this->DocsBuilderModel->getFile($this->request->get['file'], $this->request->get['group']);
            if($doc === FALSE || ! file_exists(DIR_FILES . $doc['doc_path']))
            {
                $this->view->RedirectToAction('not_found', 'Error');
            }
            return $this->view->FileResult(DIR_FILES . $doc['doc_path']);
        }
        else
            Log::write(_("not_pass_param"));
        $this->view->RedirectToAction('not_found', 'Error');
    }
    public function GetArhiv()
    {
        if( ! empty($this->request->get['zip']))
        {
            $this->join->model('Docs/Builder');
            $doc = $this->DocsBuilderModel->getZip($this->request->get['zip']);
            if($doc === FALSE)
            {
                $this->view->RedirectToAction('not_found', 'Error');
            }
            return $this->view->FileResult($doc, "Attachment", "zip");
        }
        else
            Log::write(_("not_pass_param"));
        $this->view->RedirectToAction('not_found', 'Error');
    }
}

/* End of file DocsController.php */
/* Location: ./controllers/DocsController.php */