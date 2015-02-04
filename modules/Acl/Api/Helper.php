<?php
namespace Acl\Api;

class Helper
{
	static public function hasAccess($module, $rule)
	{
		return TRUE;

		$headers = \Slim\Slim::getInstance()->request->headers;

		if(!$headers->get('user-api-key'))
		{
			return FALSE;
		}

		if(!$headers->get('user-key'))
		{
			return FALSE;
		}

		// todo get user from users module and get it's access
		// levels and then load json of levels and compare.

		return TRUE;
	}

	static public function afterGetAppModulesList($params)
	{
		//var_dump($params[2]);
		//$params[2]['acl']->test = 1;
	}
}