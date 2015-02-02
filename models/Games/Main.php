<?php
class Games_Main_model extends Object
{
	/**
	 * gets the name of the variables
	 *
	 * @access	public
	 * @return	void
	 */
	public function get_values()
	{
		// Load main model
		$this->join->model('Main');
		
		// get main values from main model
		$this->MainModel->get_main_values();
		$this->view->links = $this->MainModel->home_links();
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * gets the name of the variables
	 *
	 * @access	public
	 * @return	string
	 */
	 public function set_teams()
	 {
	 	// load library socets
		$this->join->library('socets');
		$this->socets->set_server('www.footstat.ru');
		// get content from page
		$content = $this->socets->get_content_http('/club/');
		
		if($content)
		{
			$content = str_replace(PHP_EOL, '', $content);
			$array = $this->_parse_content($content, "/<TBODY>(.*)<\/TBODY>/U", "/<a href='\/club\/(.*)<\/a>/U");
			if(count($array) > 0)
			{
				$query = "INSERT IGNORE INTO `" . DB_PREFIX . "gs_team` (`team_id`, `team_name`) VALUES ";
				foreach($array As $row)
				{
					$num	= substr($row, 0, strpos($row, '/\'>'));
					$name	= str_replace('(',' (', str_replace('	','',substr($row, strpos($row, '/\'>') + 3)));
					$query .= "(" . $num . ", '" . $name . "'), ";
				}
				$query = substr($query, 0, strlen($query) - 2) . ";";
				if($this->db->query($query))
				{
					return 'success';
				}
			}
		}
		return 'somthing wrong';
	 }
	 
	// --------------------------------------------------------------------
	 
	 /**
	 * gets the name of the variables
	 *
	 * @access	public
	 * @return	array
	 */
	 public function set_teams_defaults()
	 {
	 	// load library socets
		$this->join->library('socets');
		$this->socets->set_server('www.footstat.ru');
		$teams = $this->db->query("SELECT `team_id` As `id` FROM `" . DB_PREFIX . "gs_team` LIMIT 200");
		$array = array("answer" => "success");

		foreach ($teams->list As $value) 
		{
			
			// get content from page
			
			$content = $this->socets->get_content_http('/club/' . $value['id'] . '/');		
			if($content)
			{
				$content = str_replace(PHP_EOL, '', $content);
				
				preg_match("/Стадион<\/TD><TD>(.*)<\/TD>/U", $content, $matches) ? $stadium = $matches[1] : $stadium = '';
				preg_match("/<NOINDEX><A href='http:\/\/(.*)'>/U", $content, $matches) ? $sait = $matches[1] : $sait = '';
				
				if( ! $this->db->query("UPDATE IGNORE `" . DB_PREFIX . "gs_team` SET `team_stadium` = '" . $stadium . "', `team_sait` = '" . $sait . "' WHERE `team_id` = " . $value['id']))
				{
					$array['answer'] = "fail";
					break;
				}
			}
		}
		return $array;
	 }
	 
	// --------------------------------------------------------------------
	 
	 /**
	 * gets the name of the variables
	 *
	 * @access	protected
	 * @return	array
	 */
	 public function get_list_teams()
	 {
	 	$teams = $this->db->query("SELECT `team_id` As `id`, `team_name` As `name` FROM `" . DB_PREFIX . "gs_team` LIMIT 200");
	 	return $teams ? $teams->list : array();
	 }
	 
	// --------------------------------------------------------------------
	 
	 /**
	 * gets the name of the variables
	 *
	 * @access	protected
	 * @param	number
	 * @return	array
	 */
	 public function set_players($id = 0)
	 {
	 	$array = array("answer" => "success");
	 	$this->join->library('socets');
		$this->socets->set_server('www.footstat.ru');
		$content = $this->socets->get_content_http('/club/' . $id . '/player/');
		if($content)
		{
			$content = str_replace('','',str_replace(PHP_EOL, '', $content));
			$players = $this->_parse_content($content, "/<article>(.*)<\/article>/U", "/<tr>(.*)<\/tr>/U");
			$players = str_replace(' class=bl', '', str_replace(' class=l', '', $players));
			$query = "INSERT IGNORE `" . DB_PREFIX . "gs_people` (`people_id`, `full_name`, `birthday`, `citizenship`, `growth`, `weight`) VALUES ";
			foreach ($players As $value)
			{
				if(preg_match_all("/<td>(.*)<\/td>/U", $value, $matches))
				{
					$a = 0;
					$temp = array();
					foreach ($matches[1] As $values) {						
						switch ($a) 
						{
							case 0:
								preg_match("/\/person\/(.*)\//U", $values, $match) ? $temp['id'] = $match[1] : NULL;
								preg_match("/'>(.*)<\/a>/U", $values, $match) ? $temp['name'] = str_replace("'", "", $match[1]) : NULL;
								break;
							case 3:
								$temp['birth'] = trim($values);
								break;
							case 4:
								preg_match("/title='(.*)'/U", $values, $match) ? $temp['country'] = $match[1] : NULL;
								break;
							case 5:
								$temp['height'] = trim($values);
								break;
							case 6:
								$temp['width'] = trim($values);
								break;
							default:								
								break;
						}
						$a++;						
					}
					$query .= "(" . $temp['id'] . ", '" . $temp['name'] . "', '" . $temp['birth'] . "', '" . $temp['country'] . "', " . $temp['height'] . ", " . $temp['width'] . "),";
				}
			}
			$query = substr($query, 0, strlen($query) - 1);
			if( ! $this->db->query($query))
			{
				$array['answer'] = "fail";
				break;
			}
		}
	 	return $array;
	 }

	// --------------------------------------------------------------------
	
	/**
	 * gets the name of the variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	 public function set_matches($tournament = '1', $year = '2000')
	 {
	 	$array = array("answer" => "success");
	 	$this->join->library('socets');
		$this->socets->set_server('www.footstat.ru');
		$content = $this->socets->get_content_http('/tournament/national/rus/' . $tournament . '/' . $year . '/calendar/');
		
		if($content)
		{
			$content = str_replace('','',str_replace(PHP_EOL, '', $content));
			$matches = $this->_parse_content($content, "/<article>(.*)<\/article>/U", "/<tr>(.*)<\/tr>/U");
			$matches = str_replace(" class='r'",'', str_replace(" class='l'", "", $matches));
			$query = "";
			foreach ($matches As $value)
			{
				if(preg_match_all("/<td>(.*)<\/td>/U", $value, $val))
				{
					$a = 0;
					$temp = array('home' => '', 'match' => '', 'score' => '', 'guest' => '', 'date' => '');
					foreach ($val[1] As $field)
					{
						switch ($a) 
						{
							case 0:
								preg_match("/\/club\/(.*)\//U", $field, $find) ? $temp['home'] = $find[1] : NULL;
								break;
							case 1:
								preg_match("/\/match\/(.*)\//U", $field, $find) ? $temp['match'] = $find[1] : NULL;
								preg_match("/'>(.*)<\/a>/U", $field, $find) ? $temp['score'] = $this->_score($find[1]) : NULL;
								break;
							case 2:
								preg_match("/\/club\/(.*)\//U", $field, $find) ? $temp['guest'] = $find[1] : NULL;
								break;
							case 4:
								$temp['date'] = $this->_date($field);
								break;
							default:								
								break;
						}
						$a++;
					}
					if(array_search('', $temp) !== FALSE)
					{
						continue;
					}
					//echo $temp['home'] . ' ' . $temp['match'] . ' (' . $temp['score']['home'] . ') ' . ' (' . $temp['score']['guest'] . ') ' . $temp['guest'] . ' ' . $temp['date'] . '<br />';
					$query .= "(" . $temp['match'] . ", " . $temp['home'] . ", " . $temp['guest'] . ", " . $temp['score']['home'] . ", " . $temp['score']['guest'] . ", '" . $temp['date'] . "'),";
				}
			}
			if(strlen($query) > 0)
			{
				$query = substr($query, 0, strlen($query) - 1);
				$query = "INSERT IGNORE INTO `gs_match` (`match_id`, `team_home`, `team_guest`, `home_goals`, `guest_goals`, `match_date`) VALUES " . $query;
				if( ! $this->db->query($query))
				{
					$array['answer'] = "fail";
				}
			}
		}
		return $array;
	 }

	
	// --------------------------------------------------------------------
	
	/**
	 * gets the name of the variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	 public function set_matches_details($match_num)
	 {
	 	$array = array("answer" => "success");
	 	$this->join->library('socets');
		$this->socets->set_server('www.footstat.ru');
		$content = $this->socets->get_content_http('/match/' . $match_num . '/');
		$match_array = array();
		if($content)
		{
			$content = str_replace('','',str_replace(PHP_EOL, '', $content));
			if(preg_match("/<div class='info'>(.*)<\/div>/U", $content, $matches))
			{
				if(preg_match("/Судья: <a href=\"\/person\/(.*)\">/U", $content, $referee))
				{
					$match_array['refere'] = $this->_name_and_id(str_replace('/" title="', ' ', $referee[1]));
				}
				
				$matches[1] = substr($matches[1], strpos($matches[1], '</thead>') + 8);
				
				$treners = substr($matches[1], strrpos($matches[1], 'nover'));
				if(preg_match_all("/person\/(.*)\">/U", $treners, $treners))
				{
					foreach ($treners[1] As $value) 
					{
						$match_array['treners'][] = $this->_name_and_id(str_replace('/" title="', ' ', $value));
					}
				}
				
				while (strpos($matches[1], "<tr class='nover'>") !== FALSE) 
				{
					$matches[1] = substr($matches[1], strpos($matches[1], "<tr class='nover'>") + 18);
					if(strpos($matches[1], "<tr class='nover'>") !== FALSE)
					{
						$string = substr($matches[1], 0, strpos($matches[1], "<tr class='nover'>"));
						if(strpos($string, 'th') !== FALSE)
						{
							echo PHP_EOL . $string = str_replace("</th>",'', str_replace("<th colspan=2 class='th1'>", '', substr($string, 0, strpos($string, '</tr>'))));
						}
					}
				}
				//if(preg_match("/<article>(.*)<\/article>/U", $content, $matches))
				//{
					//
				//}
				/*
				if(preg_match_all("/<tr>(.*)<\/tr>/U", $matches[1], $matches))
				{
					$matches[1] = str_replace('td', 'i', $matches[1]);
					foreach($matches[1] As $value)
					{
						echo $value . '<br />' . PHP_EOL;
					}
				}*/
			}
			//$matches = $this->_parse_content($content, "/<article>(.*)<\/article>/U", "/<tr>(.*)<\/tr>/U");
		}
		echo serialize($match_array);
		return $array;
	 }
	 
	// --------------------------------------------------------------------
	 
	/**
	 * gets the name of the variables
	 *
	 * @access	protected
	 * @param	string
	 * @return	array
	 */
	 protected function _name_and_id($string)
	 {
	 	return array('id' => substr($string, 0, strpos($string, ' ')), 'name' => substr($string, strpos($string, ' ') + 1));
	 }
	 
	// --------------------------------------------------------------------
	 
	/**
	 * gets the name of the variables
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	protected function _parse_content($string, $pattern_one, $pattern_two)
	{
		$array = array();
		if (preg_match($pattern_one, $string, $matches))
		{
			if (preg_match_all($pattern_two, $matches[1], $matches))
			{
				$array = $matches[1];
			}
		}
		return $array;
	}
	
	// --------------------------------------------------------------------
	 
	/**
	 * gets the score
	 *
	 * @access	protected
	 * @param	string
	 * @return	array
	 */
	protected function _score($string)
	{
		$array['home']	= trim(substr($string, 0, strpos($string, ':')));
		$array['guest']	= trim(substr($string, strpos($string, ':') + 1));
		return $array;
	}
	
	// --------------------------------------------------------------------
	 
	/**
	 * gets the from string
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _date($string)
	{
		$day = trim(substr($string, 0, strpos($string, ' ')));
		$month = trim(substr($string, strpos($string, ' ') + 1));
		$month = trim(substr($month, 0, strpos($month, ' ')));
		$month = $this->_montname($month);
		$year = trim(substr($string, strrpos($string, ' ') + 1));
		return $year . '-' . $month . '-' . $day;
	}
	
	// --------------------------------------------------------------------
	 
	/**
	 * convert cirilic month to the date format
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _montname($string)
	{
		$month = '';
		switch ($string) {
			case 'января':
				$month = '01';
				break;
			case 'февраля':
				$month = '02';
				break;
			case 'марта':
				$month = '03';
				break;
			case 'апреля':
				$month = '04';
				break;
			case 'мая':
				$month = '05';
				break;
			case 'июня':
				$month = '06';
				break;
			case 'июля':
				$month = '07';
				break;
			case 'августа':
				$month = '08';
				break;
			case 'сентября':
				$month = '09';
				break;
			case 'октября':
				$month = '10';
				break;
			case 'ноября':
				$month = '11';
				break;
			case 'декабря':
				$month = '12';
				break;
				
		}
		return $month;
	}
}

/* End of file Main.php */
/* Location: ./models/Payment/Main.php */