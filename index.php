<?php

define('_API', 1);

$loader = require 'vendor/autoload.php';

$app = \Slim\Slim::getInstance();

$app
	->map('/:module/:group/:action(/:id)', function ($module, $group, $action, $id = NULL) use ($app)
	{
		$method = strtolower($app->request->getMethod());
		$file = "modules/{$module}/api/{$group}/" . ucfirst($method) . ucfirst($action) . ".php";

		try
		{
			if(!file_exists($file))
			{
				throw new \Exception('API Processor was not found!');
			}


			$data = \App\Lib\Util::_callApi($method, $module, $group, $action, $id, TRUE);

			if(!$data)
			{
				return;
			}

			if(is_array($data) || is_object($data) || $data == '1')
			{
				$app->contentType('application/json;charset=utf-8');
				$data = json_encode(array(
					'error'  => FALSE,
					'result' => $data
				));
			}
			else
			{
				$app->contentType('text/html;charset=utf-8');
			}

			$app->response->write($data);
		}
		catch(Exception $e)
		{
			$data = json_encode(array(
				'error'   => TRUE,
				'code'    => $e->getCode(),
				'message' => 'Error: ' . $e->getMessage(),
				'module'  => $module,
				'group'   => $group,
				'method'  => $method,
				'file'    => $e->getFile(),
				'line'    => $e->getLine(),
				'action'  => $action
			));

			$app->contentType('application/json;charset=utf-8');
			$app->response->write($data);
		}
		$app->stop();
	})
	->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD', 'PATCH')
	->conditions(array(
		'module' => '\w*',
		'group'  => '\w*',
		'action' => '\w*'
	));

$app->run();