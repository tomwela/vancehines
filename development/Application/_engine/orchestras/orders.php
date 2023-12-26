<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;

class OrchestraOrders extends Orchestra
{
  public  function __construct()
  {
    parent::__construct('\ClickBlocks\DB\Orders');
  }




public function codeReadOrders($params){

	if(isset($params['quantity']))$q=$params['quantity'];
		else $q=0;
	if(isset($params['unitPrice']))$p=$params['unitPrice'];
		else $p=NULL;
	if(isset($params['orderTotal']))$o=$params['orderTotal'];
		else $o=NULL;
	if(isset($params['address']))$a=$params['address'];
		else $a=NULL;
	if(isset($params['fullName']))$n=$params['fullName'];
		else $n=NULL;	
	if(isset($params['email']))$e=$params['email'];
		else $e=NULL;	
	if(isset($params['fullName']))$t=$params['tel'];
		else $t=NULL	;
	
if($q !=0){
  $this->db->cols("INSERT INTO Orders (`quantity`,unitPrice,orderTotal,address,fullName,`email`,telephone) VALUES (?,?,?,?,?,?,?)",array($q,$p,$o,$a,$n,$e,$t));
}
}

public function orderID(){

	return $this->db->col("SELECT count(order_id) FROM `Orders`");

}


}

?>

