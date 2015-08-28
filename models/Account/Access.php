<?php
class Account_Access_model extends Object {
    public function ListControllers()
    {
        $controllers = [];
        $list = new \Six_x\File(DIR_CONTROLLERS);
        $str = "";
        foreach($list->toArray() As $controller)
        {
            if(strpos($controller, "Controller") !== FALSE)
            {
                $controllers[] = substr($controller, 0, strpos($controller, "Controller"));
            }
        }

        return  $controllers;
    }
    public function GetMain()
    {
        $this->join->model('Main');
        $this->MainModel->get_main_values();
    }
    public function GetPermissions($controller)
    {
        $actions = [];
        $permissions = [];
        if(class_exists($controller . "Controller"))
        {
            $actions = $this->_getActions($controller);
            if( ! empty($actions))
            {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "permissions WHERE controller_name = '" . $controller . "'");
                if($query->count == 1)
                {
                    $permissions = unserialize($query->list[0]['permission']);
                }
            }
        }

        return ['actions' => $actions, 'pemissions' => $permissions];
    }
    protected function _getActions($controller)
    {
        $array = [];
        $methods = get_class_methods($controller . "Controller");
        foreach($methods As $method)
        {
            if(strpos($method, '__') === FALSE)
            {
                $array[] = $method;
            }
        }
        return $array;
    }
    public function ListGroups()
    {
        $groups = $this->db->query("SELECT * FROM " . DB_PREFIX . "user_roles ORDER BY role_from");
        if($groups->count > 0)
            return $groups->list;
        return [];
    }
    public function ListUsers()
    {
        $users = $this->db->query("SELECT user_id, logon_name FROM " . DB_PREFIX . "users WHERE status = 1");
        if($users->count > 0)
            return $users->list;
        return [];
    }
}