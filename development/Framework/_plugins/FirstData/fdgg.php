<?php
namespace ClickBlocks\Utils;
use \ClickBlocks\Core;

class FDGGException extends \Exception {}
class FDGGCC
{
	protected $fields = array();
	protected $data;
	public function __construct()
	{
		$this->fields = array(
			'cardnumber'   => '/^[ -\d]{0,48}$/', 
			'cardexpmonth' => '/^(0\d|1[12])$/',
			'cardexpyear'  => '/^\d\d$/',
			'track'				 => '/.*/',
			'cvmvalue'		 => '/^\d{3,4}$/',
			'cvmindicator' => '/^(provided|not_provided|illegible|not_present|no_imprint)$/',
			'cavv'				 => '/^.{28}$/i',
			'eci'					 => '/^\d\d$/',
			'xid'					 => '/^.{28}$/i',
			);
	}
	public function __set($k, $v)
	{
		if(array_key_exists($k, $this->fields))
		{
			if(Core\Register::getInstance()->config['isDebug'])
			{
				if(preg_match($this->fields[$k], $v))
					$this->data[$k] = $v;
				else throw new FDGGException("Wrong $k field: '$v';");
			} else $this->data[$k] = $v;
		} 
	}

	public function sale()
	{
		$res = array();
		if($this->data['track'])
			$required = array('track', 'cvmindicator');
		else $required = array('cardnumber', 'cardexpmonth', 'cardexpyear', 'cvmindicator');
		foreach($required as $field)
		{
			if(!$this->data[$field]) throw new FBGGException("Required Field $field is absent");
			$res[$field] = $this->data[$field];			
		}

		foreach(array('cvmvalue','cavv','eci','xid') as $f)
			if(isset($this->data[$f]))
				$res[$f] = $this->data[$f];

		return $res;
	}
}
class FDGG
{
	protected $lp = null;
	protected $baseData = array();
	protected $customer = array();

	protected $request = array();
	protected $result = null;
	protected $config = null;
	public function __construct($test = false)
	{
		$this->config = $test ? Core\Register::getInstance()->config->fdggtest : Core\Register::getInstance()->config->fdgg;
		$this->lp = new LinkPoint;
		$this->init();
	}

	protected function init()
	{
		$this->lp->setProxy($this->config['proxy']);
		$this->baseData = array(
			// 'host'				=>	'secure.linkpt.net',
			'host'				=>	$this->config['host'],
			'port'  			=>  $this->config['port'],
			'keyfile'			=>  Core\IO::dir('config')."/".$this->config['keyfile'],
			'configfile'	=>	$this->config['storenum']
			);
	}

	public function setCustomer($a)
	{
		$this->customer = $a;
		return $this;
	}

	protected function process($data)
	{
		$this->request = $data;
		$this->result = $this->lp->process($data);
		return $this->result['r_approved'] == 'APPROVED';
	}

	public function sale($data, FDGGCC $card, $authonly = false)
	{
		if(!$this->customer) throw new FDGGException('sale: no Customer Data');
		$data = array_merge($data, $this->baseData, $this->customer, $card->sale());
		$data['ordertype'] = $authonly ? 'preauth' : 'SALE';
		return $this->process($data);
	}

	public function ticket($params)
	{
		return $this->processOrder('postauth', $params);
	}

	public function void($orderID)
	{
		return $this->processOrder('VOID', $orderID);
	}

	protected function processOrder($type, $param)
	{
		$data = $this->baseData;
		if(is_array($param)) $data = array_merge($param, $data);
		else $data['oid'] = $orderID;
		$data['ordertype'] = $type;
		return $this->process($data);
	}


	public function getRequest()
	{
		return $this->request;
	}

	public function getResult()
	{
		return $this->result;
	}
}