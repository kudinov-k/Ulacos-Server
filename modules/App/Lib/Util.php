<?php

namespace App\Lib;

use \MongoClient;

class Util
{
	static public function getIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	static public function getModulesListFilter($filter)
	{
		$list = self::getModulesList();

		if(!$filter)
		{
			return $list;
		}

		$out = array();

		foreach($list as $name => $module)
		{
			$limit = explode(',', $filter);
			$meta = array_intersect_key($module, array_flip($limit));

			if(empty($meta))
			{
				continue;
			}

			$out[$name] = $meta;
		}

		return $out;
	}

	static public function getModulesList()
	{
		$list = self::getModulesNames();

		static $out = array();

		if(!empty($out))
		{
			return $out;
		}

		foreach($list as $module)
		{
			if(!file_exists($module . '/meta.json'))
			{
				continue;
			}

			$meta = json_decode(file_get_contents($module . '/meta.json'), TRUE);

			$out[basename($module)] = $meta;
		}

		return $out;
	}


	static public function getModulesNames()
	{

		static $list = array();

		if(!empty($list))
		{
			return $list;
		}

		$list = glob(APP_MODULES . '/*', GLOB_ONLYDIR);

		return $list;
	}

	static public function callApi($method, $module, $group, $action, $id, $params = NULL)
	{
		return self::_callApi($method, $module, $group, $action, $id, FALSE, $params);
	}

	static public function _callApi($method, $module, $group, $action, $id, $events = TRUE, $params = NULL)
	{
		$app = \Slim\Slim::getInstance();


		$classname = sprintf("\\%s\\Api\\%s\\%s%s", ucfirst($module), ucfirst($group), ucfirst($method), ucfirst($action));

		if(!class_exists($classname))
		{
			throw new \Exception('Cannot find class name (' . $classname . ').');
		}

		$api = new $classname($app, $module);

		if(!method_exists($api, 'execute'))
		{
			throw new \Exception('Cannot find EXECUTE method.');
		}

		if($api->require_id && !$id)
		{
			throw new \Exception('This API require ID parameter which has not been passed.');
		}

		$api->id = $id;

		if(!$api->hasAccess())
		{
			throw new \Exception('User has no access to get this data.');
		}

		if(is_array($params) && !empty($params))
		{
			foreach($params AS $pname => $pval)
			{
				$api->params[$pname] = $pval;
			}
		}

		$api->validate(APP_ROOT . "/modules/{$module}/api/{$group}/" . ucfirst($method) . ucfirst($action) . ".json");

		if($events)
		{
			$event = strtolower("before_{$method}_{$module}_{$group}_{$action}");
			\App\Lib\Events::trigger($event, array(&$api));
		}

		$data = $api->execute();

		if($events)
		{
			$event = strtolower("after_{$method}_{$module}_{$group}_{$action}");
			\App\Lib\Events::trigger($event, array(&$api, &$data));
		}

		return $data;
	}
}