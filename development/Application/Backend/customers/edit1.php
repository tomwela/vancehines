<?php

namespace ClickBlocks\MVC\Backend;

use ClickBlocks\Core,
    ClickBlocks\DB,
    ClickBlocks\Web,
    ClickBlocks\Web\UI\Helpers,
    ClickBlocks\Web\UI\POM,
    ClickBlocks\Utils;

class PageCustomerEdit extends Backend
{
	public function __construct()
	{
		parent::__construct('customers/edit.html');
	}
	
	public function access()
	{
		return true;
	}
	
	private function getData()
	{
		$srv = new \ClickBlocks\DB\ServiceCustomers();
		$usr = $srv->getByID($this->fv['id']);
		
		$aData = $usr->getValues();
		
		if(isset($this->fv['id']) && is_numeric($this->fv['id']) && $aData['fullName']) foreach($aData as $key => $value) $this->tpl->{$key} = $value ? $value : 'Not Specified';
		else \ClickBlocks\Web\JS::goURL('/admin/customers');
	}
	
	public function init()
	{
		parent::init();
		
		$this->head->name = 'Edit » Сustomers';
		$this->getData();
	}


}

?>
