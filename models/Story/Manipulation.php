<?php
class Story_Manipulation_model extends Object
{
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
				// Check titles data
			#	if( ! $this->validation->is_valid(isset($data['title'][$value['language_id']]) ? $data['title'][$value['language_id']] : NULL, 'clear_space|required|min_length[5]|max_length[200]'))
			#	{
			#		$errors[] = ' ' . $this->mui->get('title') . '[' . $value['language_id'] . '] (' . $this->validation->current_error . ');';
			#	}
			#	$array['storys'][$key]['title'] = isset($data['title'][$value['language_id']]) ? $data['title'][$value['language_id']] : "";
				$text .= $array['storys'][$key]['title'] != urldecode($value['title']) ? "title='" . urlencode($array['storys'][$key]['title']) . "'," : "";
				// Check text data
			#	if( ! $this->validation->is_valid(isset($data['text'][$value['language_id']]) ? $data['text'][$value['language_id']] : NULL, 'clear_space|required'))
			#	{
			#		$errors[] = ' ' . $this->mui->get('text') . '[' . $value['language_id'] . '] (' . $this->validation->current_error . ');';
			#	}
			#	$array['storys'][$key]['text'] = isset($data['text'][$value['language_id']]) ? $data['text'][$value['language_id']] : "";
				$text .= $array['storys'][$key]['text'] != urldecode($value['texts']) ? "texts='" . urlencode($array['storys'][$key]['text']) . "'," : "";
				// Check meta_title data
			#	if( ! $this->validation->is_valid(isset($data['meta_title'][$value['language_id']]) ? $data['meta_title'][$value['language_id']] : NULL, 'clear_space|not_null|max_length[200]'))
			#	{
			#		$errors[] = ' ' . $this->mui->get('meta_title') . '[' . $value['language_id'] . '] (' . $this->validation->current_error . ');';
			#	}
			#	$array['storys'][$key]['meta-title'] = isset($data['meta_title'][$value['language_id']]) ? $data['meta_title'][$value['language_id']] : "";
				$text .= $array['storys'][$key]['meta-title'] != urldecode($value['meta_title']) ? "meta_title='" . urlencode($array['storys'][$key]['meta-title']) . "'," : "";
				// Check meta_descr data
			#	if( ! $this->validation->is_valid(isset($data['meta_descr'][$value['language_id']]) ? $data['meta_descr'][$value['language_id']] : NULL, 'clear_space|not_null|max_length[200]'))
			#	{
			#		$errors[] = ' ' . $this->mui->get('meta_descr') . '[' . $value['language_id'] . '] (' . $this->validation->current_error . ');';
			#	}
			#	$array['storys'][$key]['description'] = isset($data['meta_descr'][$value['language_id']]) ? $data['meta_descr'][$value['language_id']] : "";
				$text .= $array['storys'][$key]['description'] != urldecode($value['meta_description']) ? "meta_description='" . urlencode($array['storys'][$key]['description']) . "'," : "";
				// Check meta_key data
			#	if( ! $this->validation->is_valid(isset($data['meta_key'][$value['language_id']]) ? $data['meta_key'][$value['language_id']] : NULL, 'clear_space|not_null|max_length[200]'))
			#	{
			#		$errors[] = ' ' . $this->mui->get('meta_key') . '[' . $value['language_id'] . '] (' . $this->validation->current_error . ');';
			#	}
			#	$array['storys'][$key]['key'] = isset($data['meta_key'][$value['language_id']]) ? $data['meta_key'][$value['language_id']] : "";
				$text .= $array['storys'][$key]['key'] != urldecode($value['meta_keyword']) ? "meta_keyword='" . urlencode($array['storys'][$key]['key']) . "'," : "";
				if(isset($data['options']))
				{
					$value['status'] = $data['options'] == 'on' ? 1 : 0;
				}
 				if(strlen($text) > 3)
 				{
 					$update[] = "UPDATE IGNORE `" . DB_PREFIX . "story_text` SET ". substr($text, 0, strlen($text) - 1) . " WHERE `language_id` = " . $value['language_id'] . " AND story_id = " . $id;
 				}
			}
			if( ! isset($array['active'])) 
			{
				$value['status'] == 1 ? $array['active'] = 'on' : $array['active'] = 'off';
			}
			if($value['status'] != $status)
			{
				$update[] = "UPDATE IGNORE `" . DB_PREFIX . "story` SET `status` = " . $value['status'] . " WHERE story_id = " . $id;
			}
			$array['summers'] .= '#text_story_' . $value['language_id'] . ', ';
		}
		
		$array['summers'] = substr($array['summers'], 0, strlen($array['summers']) -2);
		$array['link_back'] = $this->router->link('Index');

		if (count($errors) > 0)
		{
			$array['message'] = array('warning', $this->mui->get('warning'), '');
			foreach ($errors As $value) 
			{
				$array['message'][2] .= $value;
			}
		}
		elseif (count($update) > 0)
		{
			$errormessage = "";
			foreach ($update As $value)
			{
				if( ! $this->db->query($value))
				{
					$errormessage .= $this->mui->get('error_update') . " (" . $value . ")";
					Log::write("Error update " . $this->autorization->Identity['id'] . " (" . $value . ")");
				} 
				else 
				{
					Log::write("Succes update " . $this->autorization->Identity['id'] . " (" . $value . ")");
				}
			}
			$this->db->query("UPDATE `" . DB_PREFIX . "story` SET date_update = '" . date("Y-m-d H:i:s") ."' WHERE story_id = " . $id);
			if(strlen($errormessage) == 0)
			{
				$array['message'] = array('success', $this->mui->get('success'), $this->mui->get('success_update'));
			}
			else 
			{
				$array['message'] = array('warning', $this->mui->get('warning'), $errormessage);
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
						WHERE l.lang = '" . $this->mui->get_lang() . "'
						ORDER BY l.sort_order LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($text_query);	
		$array['storys'] = $query->count > 0 ? $query->list : array();
		$array['link_edit'] = $this->router->link('Edit');
		$array['link_create'] = $this->router->link('Create');
		$array['delete'] = $this->mui->get("button_delete");
		$array['permanently'] = $this->mui->get("message_permanently");
		$array['close'] = $this->mui->get("button_close");
		$array['submit'] = $this->mui->get("button_submit");
		$array['create'] = $this->mui->get("button_create");
		$array['edit'] = $this->mui->get("button_edit");
		
		return $array;
	}
	private function _get_main()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$this->mui->load('Story/Edit');
		
		$array = array(
			'title'			=> $this->mui->get('text_edit'), 
			'title_text'	=> $this->mui->get('entry_title'),
			'text_body'		=> $this->mui->get('entry_description'), 
			'meta_title'	=> $this->mui->get('entry_meta_title'),
			'meta_descr'	=> $this->mui->get('entry_meta_description'),
			'meta_key'		=> $this->mui->get('entry_keyword'),
			'language'		=> array(
					'locale'	=> $this->mui->get_lang(), 
					'name'		=> $this->mui->get_name()
					)
			);
					
		$file_localization = DIR_SCRIPTS . 'localization/' . 'summernote-' . $this->mui->get_lang() . '.js';
		
		if (file_exists($file_localization))
		{
			$array =array_merge($array, array(
				'localization_summer' => '<script src="' . HTTP_SERVER . $file_localization .'"></script>' . PHP_EOL, 'lang_summer' => $this->mui->get_lang()
				)
			);
		}
		
		$array['buttons'] = array(
			'cancel'	=> $this->mui->get('button_cancel'), 
			'save'		=> $this->mui->get('button_save'),
			'on'		=> $this->mui->get('button_on'),
			'off'		=> $this->mui->get('button_off')
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
			$array['message'] = array('warning', $this->mui->get('warning'), '');
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
			if( ! $this->db->query($max))
			{
				$errormessage .= $this->mui->get('error_create') . " (" . $max . ")";
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
						
					if( ! $this->db->query($text_query))
					{
						$errormessage .= $this->mui->get('error_create') . " (" . $text_query . ")";
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
				$this->session->data['message'] = array('success', $this->mui->get('success'), $this->mui->get('success_create'));
				$this->view->RedirectToAction('Edit', 'Story', array('story' => $id));
			}
			else 
			{
				$array['message'] = array('warning', $this->mui->get('warning'), $errormessage);
			}
		}
		$array['summers'] = substr($array['summers'], 0, strlen($array['summers']) -2);
		$array['link_back'] = $this->router->link('Index');
		$array['link_form'] = $this->router->Link('Create');
		
		return $array;
	}
	protected function _checkData($data, $id)
	{
		$array = array();
		$errors = array();
		// Check titles data
		if( ! $this->validation->is_valid(isset($data['title'][$id]) ? $data['title'][$id] : NULL, 'clear_space|required|min_length[5]|max_length[200]'))
		{
			$errors[] = ' ' . $this->mui->get('title') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['title'] = isset($data['title'][$id]) ? $data['title'][$id] : "";

		// Check text data
		if( ! $this->validation->is_valid(isset($data['text'][$id]) ? $data['text'][$id] : NULL, 'clear_space|required'))
		{
			$errors[] = ' ' . $this->mui->get('text') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['text'] = isset($data['text'][$id]) ? $data['text'][$id] : "";

		// Check meta_title data
		if( ! $this->validation->is_valid(isset($data['meta_title'][$id]) ? $data['meta_title'][$id] : NULL, 'clear_space|not_null|max_length[200]'))
		{
			$errors[] = ' ' . $this->mui->get('meta_title') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['meta-title'] = isset($data['meta_title'][$id]) ? $data['meta_title'][$id] : "";

		// Check meta_descr data
		if( ! $this->validation->is_valid(isset($data['meta_descr'][$id]) ? $data['meta_descr'][$id] : NULL, 'clear_space|not_null|max_length[200]'))
		{
			$errors[] = ' ' . $this->mui->get('meta_descr') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['description'] = isset($data['meta_descr'][$id]) ? $data['meta_descr'][$id] : "";

		// Check meta_key data
		if( ! $this->validation->is_valid(isset($data['meta_key'][$id]) ? $data['meta_key'][$id] : NULL, 'clear_space|not_null|max_length[200]'))
		{
			$errors[] = ' ' . $this->mui->get('meta_key') . '[' . $id . '] (' . $this->validation->current_error . ');';
		}
		$array['key'] = isset($data['meta_key'][$id]) ? $data['meta_key'][$id] : "";
		
		return array($array, $errors);	
	}
	public function delete_storys($array = array())
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
			if( ! $this->db->query($text_query))
			{
				$errormessage .= $this->mui->get('error_delete') . " (" . $text_query . ")";
				Log::write("Delete error: ". $this->autorization->Identity['id'] . " (" . $text_query . ")");
				
				$array['message'] = array('warning', $this->mui->get('warning'), $this->mui->get('error_delete'));
			}
			else 
			{
				Log::write("Succes delete " . $this->autorization->Identity['id'] . " (" . $text_query . ")");
				$array['message'] = array('success', $this->mui->get('success'), $this->mui->get('success_delete'));
			}
		}
		return $array;
	}
}

/* End of file Manipulation.php */
/* Location: ./models/Story/Manipulation.php */