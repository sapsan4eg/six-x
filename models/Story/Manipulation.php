<?php
class Story_Manipulation_model extends Object {
	public function story($id = 0, $data = array())
	{
		$array = $this->_get_main();
		$errors = array();
		$update = array();

		$text_query = "SELECT s.*, t.*, l.name, l.lang 
						FROM `" . DB_PREFIX . "story` s 
						LEFT JOIN `" . DB_PREFIX . "story_text` t ON (s.story_id = t.story_id) 
						LEFT JOIN `" . DB_PREFIX . "language` l ON (l.language_id = t.language_id) 
						WHERE s.story_id = " . $id . " AND l.status = 1
						ORDER BY l.sort_order";
						
		$query = $this->db->query($text_query);
		if($query->count == 0)
		{
			return FALSE;
		}
		$array['summers'] = '';
		
		// load library validation
		count($data) > 0 ? $this->join->library('validation') : '';	
		
		foreach ($query->list As $key => $value) 
		{
			$array['storys'][$key] = array(
				'langid'	=> $value['language_id'], 
				'title'		=> urldecode($value['title']), 
				'text'		=> urldecode($value['texts']), 
				'meta-title'	=> urldecode($value['meta_title']), 
				'description'	=> urldecode($value['meta_description']),
				'key'		=> urldecode($value['meta_keyword']), 
				'name'		=> $value['name'], 
				'locale'	=> $value['lang']
				);
			$status = $value['status'];
			if(count($data) > 0)
			{
				$temp = $this->_checkData($data, $value['language_id']);
				$array['storys'][$key] = array_merge($array['storys'][$key], $temp[0]);
				$errors = array_merge($errors, $temp[1]);
				$text = "";				
				$text .= $array['storys'][$key]['title'] != urldecode($value['title']) ? "title='" . urlencode($array['storys'][$key]['title']) . "'," : "";
				$text .= $array['storys'][$key]['text'] != urldecode($value['texts']) ? "texts='" . urlencode($array['storys'][$key]['text']) . "'," : "";
				$text .= $array['storys'][$key]['meta-title'] != urldecode($value['meta_title']) ? "meta_title='" . urlencode($array['storys'][$key]['meta-title']) . "'," : "";
				$text .= $array['storys'][$key]['description'] != urldecode($value['meta_description']) ? "meta_description='" . urlencode($array['storys'][$key]['description']) . "'," : "";
				$text .= $array['storys'][$key]['key'] != urldecode($value['meta_keyword']) ? "meta_keyword='" . urlencode($array['storys'][$key]['key']) . "'," : "";
				if(isset($data['options']))
                    $value['status'] = $data['options'] == 'on' ? 1 : 0;
 				if(strlen($text) > 3)
                    $update[] = "UPDATE IGNORE `" . DB_PREFIX . "story_text`
                                    SET ". substr($text, 0, strlen($text) - 1) . "
                                    WHERE `language_id` = " . $value['language_id'] . "
                                    AND story_id = " . $id;
			}
			if( ! isset($array['active']))
                $value['status'] == 1 ? $array['active'] = 'on' : $array['active'] = 'off';

			if($value['status'] != $status)
                $update[] = "UPDATE IGNORE `" . DB_PREFIX . "story` SET `status` = " . $value['status'] . " WHERE story_id = " . $id;

			$array['summers'] .= '#text_story_' . $value['language_id'] . ', ';
		}
		
		$array['summers'] = substr($array['summers'], 0, strlen($array['summers']) -2);
		$array['link_back'] = $this->router->link('Index');

		if (count($errors) > 0)
		{
			$array['message'] = array('warning', _('warning'), '');
			foreach ($errors As $value) 
			{
				$array['message'][2] .= $value;
			}
		}
		elseif (count($update) > 0)
		{
			$errormessage = '';
			foreach ($update As $value)
			{
				if( ! $this->db->query($value, TRUE))
				{
					$errormessage .= _('error_update') . " (" . $value . ")";
					Log::write("Error update " . $this->autorization->Identity['id'] . " (" . $value . ")");
				} 
				else 
				{
					Log::write("Succes update " . $this->autorization->Identity['id'] . " (" . $value . ")");
				}
			}
			$this->db->query("UPDATE `" . DB_PREFIX . "story` SET date_update = '" . date("Y-m-d H:i:s") ."' WHERE story_id = " . $id, TRUE);
			if(strlen($errormessage) == 0)
			{
				$array['message'] = array('success', _('success'), _('success_update'));
			}
			else 
			{
				$array['message'] = array('warning', _('warning'), $errormessage);
			}
		}
		return $array;
	}
	public function get_storys($start = 0, $limit = 10)
	{
		$array = $this->_get_main();

		$text_query = "SELECT s.story_id, s.sort_order, t.title
						FROM `" . DB_PREFIX . "story` s 
						LEFT JOIN `" . DB_PREFIX . "story_text` t ON (s.story_id = t.story_id) 
						LEFT JOIN `" . DB_PREFIX . "language` l USING (language_id) 
						WHERE l.lang = '" . Six_x\Mui::get_lang() . "'
						ORDER BY l.sort_order LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($text_query);	
		$array['storys'] = $query->count > 0 ? $query->list : array();
		$array['link_edit'] = $this->router->link('Edit');
		$array['link_create'] = $this->router->link('Create');
		$array['delete'] = _("button_delete");
		$array['permanently'] = _("message_permanently");
		$array['close'] = _("button_close");
		$array['submit'] = _("button_submit");
		$array['create'] = _("button_create");
		$array['edit'] = _("button_edit");
		
		return $array;
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
					
		$file_localization = DIR_SCRIPTS . 'localization/' . 'summernote-' . Six_x\Mui::get_lang() . '.js';
		
		if (file_exists($file_localization))
		{
			$array =array_merge($array, array(
				'localization_summer' => '<script src="' . HTTP_SERVER . $file_localization .'"></script>' . PHP_EOL, 'lang_summer' => Six_x\Mui::get_lang()
				)
			);
		}
		
		$array['buttons'] = array(
			'cancel'	=> _('button_cancel'),
			'save'		=> _('button_save'),
			'on'		=> _('button_on'),
			'off'		=> _('button_off')
		);
			
		return $array;
	}
	public function show_story($id = 0, $language = 0)
	{
		$text_story = "SELECT t.* FROM `" . DB_PREFIX . "story_text` t
				JOIN `" . DB_PREFIX . "language` l USING (language_id) 
				WHERE lang = '" . $language . "' AND story_id=" . $id;
		$query = $this->db->query($text_story);
		return $query->first;
	} 
	public function create($data = array())
	{
		$errors = array();
		$array = $this->_get_main();
		$text_query = "SELECT * FROM `" . DB_PREFIX . "language` WHERE `status` = 1 ORDER BY `sort_order`";
		$query = $this->db->query($text_query);
		$array['summers'] = '';
		
		// load library validation
		count($data) > 0 ? $this->join->library('validation') : '';

		foreach ($query->list As $key => $value) 
		{
			$array['storys'][$key] = array(
				'langid'	=> $value['language_id'], 
				'title'		=> '', 
				'text'		=> '', 
				'meta-title'	=> '', 
				'description'	=> '',
				'key'		=> '', 
				'name'		=> $value['name'], 
				'locale'	=> $value['lang']
				);
			$array['summers'] .= '#text_story_' . $value['language_id'] . ', ';
			
			if(count($data) > 0)
			{
				$temp = $this->_checkData($data, $value['language_id']);
				$array['storys'][$key] = array_merge($array['storys'][$key], $temp[0]);
				$errors = array_merge($errors, $temp[1]);
			}
		}
		
		if (count($errors) > 0)
		{
			$array['message'] = array('warning', _('warning'), '');
			foreach ($errors As $value) 
			{
				$array['message'][2] .= $value;
			}
		}
		elseif (count($data) > 0)
		{
			$errormessage = "";
			$status = 1;
			if(isset($data['options']))
			{
				$status= $data['options'] == 'on' ? 1 : 0;
			}
			$max = "INSERT INTO `" . DB_PREFIX . "story` (`sort_order`, status, date_create, date_update)
					SELECT MAX(s.sort_order) + 1 sort_order, " . $status . " status, '" . date("Y-m-d H:i:s") . "' date_create, '" . date("Y-m-d H:i:s") . "' date_update 
					FROM story s";
			if( ! $this->db->query($max, TRUE))
			{
				$errormessage .= _('error_create') . " (" . $max . ")";
				Log::write("Error create " . $this->autorization->Identity['id'] . " (" . $max . ")");
			} 
			else 
			{
				Log::write("Succes create " . $this->autorization->Identity['id'] . " (" . $max . ")");
				$id = $this->db->getLastId();
				
				foreach ($array['storys'] As $value)
				{
					$text_query = "INSERT INTO story_text (story_id, language_id, title, texts, meta_title, meta_description, meta_keyword)
						SELECT " . $id . " story_id, language_id, 
						'" . urlencode($value['title']) . "' title, 
						'" . urlencode($value['text']) . "' texts, 
						'" . urlencode($value['meta-title']) . "' meta_title, 
						'" . urlencode($value['description']) . "' meta_description, 
						'" . urlencode($value['key']) . "' meta_keyword
						FROM `language` WHERE `lang` = '" . $value['locale'] . "'";
						
					if( ! $this->db->query($text_query, TRUE))
					{
						$errormessage .= _('error_create') . " (" . $text_query . ")";
						Log::write("Create error ". $this->autorization->Identity['id'] . " (" . $text_query . ")");
					} 
					else 
					{
						Log::write("Succes create " . $this->autorization->Identity['id'] . " (" . $text_query . ")");
					}
				}
			}

			if(strlen($errormessage) == 0)
			{
				$this->session->data['message'] = array('success', _('success'), _('success_create'));
				$this->view->RedirectToAction('Edit', 'Story', array('story' => $id));
			}
			else 
			{
				$array['message'] = array('warning', _('warning'), $errormessage);
			}
		}
		$array['summers'] = substr($array['summers'], 0, strlen($array['summers']) -2);
		$array['link_back'] = $this->router->link('Index');
		$array['link_form'] = $this->router->Link('Create');
		
		return $array;
	}

    // --------------------------------------------------------------------

    /**
     * Validate data from request
     *
     * @param	array
     * @param	int
     * @return	array
     */
	protected function _checkData($data, $id)
	{
		$array = [];
		$errors = [];
		// Check titles data
		if( ! $this->validation->is_valid(isset($data['title'][$id]) ? $data['title'][$id] : NULL, 'clear_space|required|min_length[5]|max_length[200]'))
		{
			$errors[] = ' ' . _('title') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['title'] = isset($data['title'][$id]) ? $data['title'][$id] : "";

		// Check text data
		if( ! $this->validation->is_valid(isset($data['text'][$id]) ? $data['text'][$id] : NULL, 'clear_space|required'))
		{
			$errors[] = ' ' . _('text') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['text'] = isset($data['text'][$id]) ? $data['text'][$id] : "";

		// Check meta_title data
		if( ! $this->validation->is_valid(isset($data['meta_title'][$id]) ? $data['meta_title'][$id] : NULL, 'clear_space|not_null|max_length[200]'))
		{
			$errors[] = ' ' . _('meta_title') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['meta-title'] = isset($data['meta_title'][$id]) ? $data['meta_title'][$id] : "";

		// Check meta_descr data
		if( ! $this->validation->is_valid(isset($data['meta_descr'][$id]) ? $data['meta_descr'][$id] : NULL, 'clear_space|not_null|max_length[200]'))
		{
			$errors[] = ' ' . _('meta_descr') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['description'] = isset($data['meta_descr'][$id]) ? $data['meta_descr'][$id] : "";

		// Check meta_key data
		if( ! $this->validation->is_valid(isset($data['meta_key'][$id]) ? $data['meta_key'][$id] : NULL, 'clear_space|not_null|max_length[200]'))
		{
			$errors[] = ' ' . _('meta_key') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['key'] = isset($data['meta_key'][$id]) ? $data['meta_key'][$id] : "";
		
		return [$array, $errors];
	}

    // --------------------------------------------------------------------

    /**
     * Delete story from db
     *
     * @param	array
     * @return	array
     */
	public function delete_storys($array = [])
	{
		$text_query = "";
		
		foreach ($array As $value)
		{
			$text_query .= $value . ",";
		}
		$array = array();
		if(strlen($text_query) > 0)
		{
			$text_query = "DELETE FROM `" . DB_PREFIX . "story` WHERE `story_id` IN (".substr($text_query, 0, strlen($text_query) - 1) . ")";
			if( ! $this->db->query($text_query, TRUE))
			{
				Log::write("Delete error: ". $this->autorization->Identity['id'] . " (" . $text_query . ")");
				
				$array['message'] = array('warning', _('warning'), _('error_delete'));
			}
			else 
			{
				Log::write("Succes delete " . $this->autorization->Identity['id'] . " (" . $text_query . ")");
				$array['message'] = array('success', _('success'), _('success_delete'));
			}
		}
		return $array;
	}
}

/* End of file Manipulation.php */
/* Location: ./models/Story/Manipulation.php */