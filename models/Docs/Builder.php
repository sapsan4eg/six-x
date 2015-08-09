<?php
class Docs_Builder_model extends Object {

    public function getList($from = 0, $step = 10)
    {
        $this->_get_main();
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "docs_groups
                    WHERE status = 1
                    ORDER BY date_create DESC LIMIT " . $from . "," . $step);

        $count = $this->db->query("SELECT COUNT(1) count FROM " . DB_PREFIX . "docs_groups WHERE status = 1")->first['count'];
        $array = [];
        foreach($result->list As $group)
        {
            #$array[$value['docs_group_id']]['group'] = $value;
            $docs = $this->db->query("SELECT * FROM " . DB_PREFIX . "docs
                WHERE group_id = " . $group['docs_group_id'] . " AND status = 1
                ORDER BY date DESC");
            $array[$group['docs_group_id']] = ['group' => $group, 'docs' => $docs->list];
        }
        return [$count, $array];
    }
    public function getFile($name, $group)
    {
        $len = strlen($name);
        $name = \Six_x\Protecter::xss_clean($name);
        $name = \Six_x\Protecter::injection_clear($name);
        $name = \Six_x\Protecter::file_up($name);

        if($len == strlen($name))
        {
            $doc = $this->db->query("SELECT * FROM " . DB_PREFIX . "docs
                WHERE doc_path = '" . $name . "' AND group_id = " . (int)$group . " AND status = 1 LIMIT 1");
            if($doc->count > 0)
                return $doc->first;
            else
                Log::write(_('error_download') . " " . $name);
        }
        else
            Log::write(_("corrupted_filename") . " " . $name);
        return FALSE;
    }
    public function getZip($id)
    {
        $docs = $this->db->query("SELECT d.* FROM " . DB_PREFIX . "docs_groups g
        JOIN " . DB_PREFIX . "docs d
        ON(d.group_id = g.docs_group_id AND d.group_id = " . (int)$id . " AND g.status = 1 AND d.status = 1)");

        if($docs->count > 0)
        {
            $zip = new \Six_x\Zip();
            foreach($docs->list As $doc)
            {
                $zip->add_data(DIR_FILES . $doc['doc_path']);
            }
            return $zip->get_zip();
        } else
            Log::write(_('error_download') . " group - " . $id);
        return FALSE;
    }

    private function _get_main()
    {
        $this->join->model('Main');
        $this->MainModel->get_main_values();

        $array = array(
            'title'			=> _('story.text_edit'),
            'title_text'	=> _('story.entry_title'),
            'text_body'		=> _('story.entry_description'),
            'meta_title'	=> _('story.entry_meta_title'),
            'meta_descr'	=> _('story.entry_meta_description'),
            'meta_key'		=> _('story.entry_keyword'),
            'language'		=> array(
                'locale'	=> Six_x\Mui::get_lang(),
                'name'		=> Six_x\Mui::get_name()
            )
        );
        return $array;
    }
}

/* End of file Builder.php */
/* Location: ./models/Docs/Builder.php */