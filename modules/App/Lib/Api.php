<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 12/11/14
 * Time: 18:03
 */

namespace App\Lib;

class Api
{
	/**
	 * @var \Slim\Slim
	 */
	public $app;

	/**
	 * @var \MongoDB
	 */
	public $db;

	/**
	 * @var string
	 */
	public $access = '';

	/**
	 * @var string
	 */
	public $id = '';

	/**
	 * @var bool
	 */
	public $require_id = FALSE;

	/**
	 * @var bool
	 */
	public $require_db = TRUE;

	/**
	 * @var array default parameters.
	 */
	public $params = array();


	public function __construct($app, $module)
	{
		$this->app = $app;
		$this->module = $module;
	}

	public function hasAccess()
	{
		if(empty($this->access))
		{
			return TRUE;
		}

		return \Acl\Api\Helper::hasAccess($this->module, $this->access);
	}

	public function validate($json)
	{
		if(!file_exists($json))
		{
			return TRUE;
		}

		$meta = json_decode(file_get_contents($json));

		foreach($meta->params AS $param)
		{
			if(!empty($param->default) && empty($this->params[$param->name]))
			{
				$this->params[$param->name] = $this->app->request->params($param->name, $param->default);
			}

			if(@$param->required && !$this->get($param->name, $param->default))
			{
				throw new \Exception("Parameter {$param->name} is required");
			}

			if(!empty($param->validate->name) && $this->get($param->name, $param->default))
			{
				$this->{"validate_" . $param->validate->name}($this->get($param->name, $param->default), @$param->validate->rule);
			}
		}

		return TRUE;
	}

	public function get($name, $default = NULL)
	{
		if(array_key_exists($name, $this->params))
		{
			return $this->params[$name];
		}

		return $this->app->request->params($name, $default);
	}

	protected function validate_number($value, $rule)
	{
		if(!preg_match('/^[0-9]*$/iU', $value))
		{
			throw new \Exception("Parameter {$value} may be only digits.");
		}

		return TRUE;
	}

	protected function validate_regex($value, $rule)
	{
		if(!preg_match($rule, $value))
		{
			throw new \Exception("Parameter {$value} does not match required format");
		}

		return TRUE;
	}
}