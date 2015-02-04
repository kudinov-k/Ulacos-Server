<?php
namespace Acl\Api\Levels;

if(!defined('_API'))
	exit;

class DeleteLevel extends \App\Lib\Api
{
	public $require_id = TRUE;
	public $access = 'delete.levels';

	public function execute()
	{
		return 1;
	}
}

