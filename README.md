# Backend (API)

This is backend part of the Ulacos. Here you define your module, and build logic. All modules have to be in `modules` folder.
 
## Structure

Here is the structure of typical Ulacos module as example `Users`.

	modules
	`-- users
		|-- api
		|-- lib
		|-- i18n
		|	|-- en-GB.users.json
		|	`-- ru-RU.users.json
		|-- acl.json
		`-- meta.json

Now let's me describe purpose of every folder and file.

### api

Folder `api` is there for RESTFull API interface. There is a special file structure inside to create API requests. For example you want to get list of users. You will end up with `GET` request to URL like this.

	https://apiserver.com/[sub_folder]/users/manage/list/

We are looking into `/users/manage/list`, because `https://apiserver.com/[sub_folder]/` is just a root for `index.php` of this backend. If we break this URL apart we will see 4 elements.

1. Method is `GET`.
2. Module is `users`.
3. API requests group `manage`. Groups are created to enhance file organization. If you have a lot of APIs, it is cool to organize then into sort of categories/groups.
4. API name `list`.

To make this API working we have to create file.

	modules/[module]/api/[group]/[method][name].php

So this will be a file

	modules/users/api/manage/GetList.php

Now inside this file we have to create something like this

	<?php
	namespace Users\Api\Manage;
	
	if(!defined('_API')) exit;
	
	class GetList extends \App\Api {
	
		public $access = 'manage.list';
		public $require_id = true;
	
		public function execute(){
			// make DB calls and return JSON
		}
	}

We will discuss how to make DB calls and what parameters do mean later in a separate article. Right now there are 3 main things to understand.

1. Namespace have to be capitalized `[Module name]\Api\[Group]`.
2. Class name have to match the name of the file.
3. Class HAVE to extend of `\App\Api`.
4. Class have to have `execute()` method.

### lib

This folder where you can create and store all your PHP helper files that may be used by you or other modules.

### i18n

This is folder where all language strings are located. This will be requested by client (website) and used for translations.
 
### acl.json

This file contains rules that you ever check against. YOu define rules there, user will be able to set different user groups, different permissions and you will be able to check if current user have permission for your rule or rule of other module. 

### meta.json

This file contain module parameters with tells client how it will behave and where it will be used.