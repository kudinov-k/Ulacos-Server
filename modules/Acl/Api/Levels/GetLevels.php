<?php
namespace Acl\Api\Levels;

if(!defined('_API'))
	exit;

class GetLevels extends \App\Lib\Api
{

	public $access = 'select.levels';

	public function execute()
	{
		return $this->db->selectCollection('levels')->find();
	}
}