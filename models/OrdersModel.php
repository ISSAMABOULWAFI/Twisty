<?php
class OrdersModel {
	public static function getAllStates($lang=1){
		$sql = 'SELECT * '.
		'FROM '._DB_PREFIX_.'order_state_lang '.
		'WHERE    id_lang=\''.$lang.'\'';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}
	public static function getOrdersDetail($stat){
		$sql = 'SELECT od.id_order_detail,o.id_order,product_id,product_name,product_quantity,product_ean13 '.
		'FROM '._DB_PREFIX_.'order_detail od '.
		'LEFT JOIN '._DB_PREFIX_.'orders o ON od.id_order=o.id_order '.
		'WHERE   1=1  and o.current_state IN ('.$stat.')';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}
	
	public static function addTwistyOrder($arr){
		if(Db::getInstance()->insert('twisty_orders', $arr)){
			return Db::getInstance()->Insert_ID();
		}
		return -1;
	}
	public static function getTwistyBoxesNames(){
		
		$sql = 'SELECT distinct(id_box) '.
				'FROM '._DB_PREFIX_.'twisty_orders where is_finished=0';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}
	public static function getTwistyOrdersDetail(){
		/*$sql = 'SELECT id_twisty,id_order,product_ean13,product_quantity,tor.id_order_detail,qte_picked,id_box,is_finished,date_twisty '.
		'FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail ';
		*/
		$sql = 'SELECT id_twisty,product_name,ord.id_order,ord.total_paid_tax_incl,od.product_reference,product_ean13,product_quantity,tor.id_order_detail,qte_picked,id_box,is_finished,date_twisty,payment,total_shipping '.
		'FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail '.
		'LEFT JOIN '._DB_PREFIX_.'orders ord ON  ord.id_order=od.id_order '.
		'WHERE ord.id_order in ('.
		'	SELECT id_order '.
		'	FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'	LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail '.
		'	GROUP BY id_order '.
		'	HAVING count(tor.id_order_detail)=sum(is_valid) and count(tor.id_order_detail)=sum(tor.is_show) '.
		') '.
		'ORDER BY id_box,product_ean13';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}
	public static function getTwistyOrdersDetailRows(){
		/*$sql = 'SELECT id_twisty,id_order,product_ean13,product_quantity,tor.id_order_detail,qte_picked,id_box,is_finished,date_twisty '.
		'FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail ';
		*/
		$sql = 'SELECT id_twisty,product_name,ord.id_order,ord.total_paid_tax_incl,od.product_reference,product_ean13,product_quantity,tor.id_order_detail,qte_picked,id_box,is_finished,date_twisty,payment,total_shipping,is_show '.
		'FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail '.
		'LEFT JOIN '._DB_PREFIX_.'orders ord ON  ord.id_order=od.id_order '.
		'WHERE ord.id_order in ('.
		'	SELECT id_order '.
		'	FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'	LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail '.
		'	GROUP BY id_order '.
		'	HAVING count(tor.id_order_detail)=sum(is_valid) '.
		') '.
		'ORDER BY id_box,product_ean13';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}
	public static function executeRequette($sql){
		if(Db::getInstance()->execute($sql)){
			return true;
		}else{
			return false;
		}
	}
	public static function removetwistylist(){
		if(Db::getInstance()->delete('twisty_orders', 'is_finished = 0')){
			return true;
		}else{
			return false;
		}
	}
	
	public static function removealltwistylist(){
		if(Db::getInstance()->delete('twisty_orders')){
			return true;
		}else{
			return false;
		}
	}
	
	public static function removeFinishedOrders($id){
		/*$ids="";
		foreach($list as $id){
			$ids.="'".$id."',";
		}
		$ids=substr($ids, 0, -1);
		*/
/*		$req='update '._DB_PREFIX_.'twisty_orders set is_show="0" where id_twisty in 
		( select id_twisty from (SELECT id_twisty 
		 FROM '._DB_PREFIX_.'twisty_orders tor 
		 left join  '._DB_PREFIX_.'order_detail od on tor.id_order_detail=od.id_order_detail 
		 WHERE od.id_order="'.$id.'") AS c )';
 */
 

		$req='delete from '._DB_PREFIX_.'twisty_orders where id_twisty in 
		( select id_twisty from (SELECT id_twisty 
		 FROM '._DB_PREFIX_.'twisty_orders tor 
		 left join  '._DB_PREFIX_.'order_detail od on tor.id_order_detail=od.id_order_detail 
		 WHERE od.id_order="'.$id.'") AS c )';
 
 
		if(Db::getInstance()->execute($req)){
			return true;
		}else{
			return false;
		}
	}
	public static function desableTwistyOrder($id){
		/*$ids="";
		foreach($list as $id){
			$ids.="'".$id."',";
		}
		$ids=substr($ids, 0, -1);
		*/
		$req='delete from '._DB_PREFIX_.'twisty_orders where id_twisty in 
		( select id_twisty from (SELECT id_twisty 
		 FROM '._DB_PREFIX_.'twisty_orders tor 
		 left join  '._DB_PREFIX_.'order_detail od on tor.id_order_detail=od.id_order_detail 
		 WHERE od.id_order="'.$id.'") AS c )';
 
 
		if(Db::getInstance()->execute($req)){
			return true;
		}else{
			return false;
		}
	}
	public static function resetOrder($id){
		/*$ids="";
		foreach($list as $id){
			$ids.="'".$id."',";
		}
		$ids=substr($ids, 0, -1);
		*/
		$req='update '._DB_PREFIX_.'twisty_orders set qte_picked="0" where id_twisty in 
		( select id_twisty from (SELECT id_twisty 
		 FROM '._DB_PREFIX_.'twisty_orders tor 
		 left join  '._DB_PREFIX_.'order_detail od on tor.id_order_detail=od.id_order_detail 
		 WHERE od.id_order="'.$id.'") AS c )';
 
 
		if(Db::getInstance()->execute($req)){
			return true;
		}else{
			return false;
		}
	}
	
	public static function removeTwistyOrders($orders_list){
		/*$ids="";
		foreach($list as $id){
			$ids.="'".$id."',";
		}
		$ids=substr($ids, 0, -1);
		*/
		$res="";
		foreach($orders_list as $order){
			$res.="'".$order['id_order']."',";
		}
		$res= substr($res, 0, -1);
		
		$req='delete from '._DB_PREFIX_.'twisty_orders where id_twisty in 
		( select id_twisty from (SELECT id_twisty 
		 FROM '._DB_PREFIX_.'twisty_orders tor 
		 left join  '._DB_PREFIX_.'order_detail od on tor.id_order_detail=od.id_order_detail 
		 WHERE od.id_order in ('.$res.') ) AS c )';
 
 
		if(Db::getInstance()->execute($req)){
			return true;
		}else{
			return false;
		}
	}
	
	public static function getTwistyListForFront(){
		/*$sql = 'SELECT id_twisty,id_order,product_ean13,product_quantity,tor.id_order_detail,qte_picked,id_box,is_finished,date_twisty '.
		'FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail ';
		*/
		$sql = 'SELECT id_twisty,product_name,ord.id_order,ord.total_paid_tax_incl,od.product_reference,product_ean13,product_quantity,tor.id_order_detail,qte_picked,id_box,is_finished,date_twisty,payment,total_shipping '.
		'FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail '.
		'LEFT JOIN '._DB_PREFIX_.'orders ord ON  ord.id_order=od.id_order '.
		'WHERE ord.id_order in ('.
		'	SELECT id_order '.
		'	FROM '._DB_PREFIX_.'twisty_orders  tor '.
		'	LEFT JOIN '._DB_PREFIX_.'order_detail od ON  od.id_order_detail=tor.id_order_detail '.
		'	GROUP BY id_order '.
		'	HAVING count(tor.id_order_detail)=sum(is_valid) and count(tor.id_order_detail)=sum(tor.is_show) '.
		') '.
		'ORDER BY id_box,product_ean13';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
	}
}


?>