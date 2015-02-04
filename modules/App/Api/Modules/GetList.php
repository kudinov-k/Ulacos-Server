<?php
namespace App\Api\Modules;

if(!defined('_API'))
	exit;

use \App\Lib\Util;
use \App\Lib\Api;

class GetList extends Api
{
	public $require_db = FALSE;

	public function execute()
	{
		return Util::getModulesListFilter($this->get('fields'));
	}
}