<?php
namespace Acl\Api\Levels;

if(!defined('_API'))
	exit;

class PostLevel extends \App\Lib\Api
{

	public function execute()
	{
		$levels = $this->db->selectCollection('levels');
		$levels->insert(json_decode($this->app->request->getBody(), TRUE));
	}
}


