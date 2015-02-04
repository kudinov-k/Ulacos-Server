<?php
namespace App\Lib;

if(!defined('_API'))
	exit;

use \App\Lib\Util;

class Events
{
	static $events = array();

	static public function trigger($event, $args)
	{
		if(empty(self::$events)) {
			self::_load_events();
		}

		if(empty(self::$events[$event]))
		{
			return;
		}

		$name = str_replace('_', '', $event);
		$events = self::$events[$event];

		foreach($events AS $class)
		{
			if(!class_exists($class)) {
				continue;
			}

			$class::$name($args);
		}


	}

	static private function _load_events()
	{

		$modules = Util::getModulesListFilter('events');

		foreach($modules AS $name => $module)
		{
			if(empty($module['events']))
			{
				continue;
			}

			foreach($module['events'] AS $event => $class_name)
			{
				self::$events[$event][] = $class_name;
			}
		}
	}
}