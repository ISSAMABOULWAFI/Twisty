<?php


if(!defined('_PS_VERSION_'))
	exit();


require_once '../classes/order/Order.php';
require_once '../classes/order/OrderHistory.php';

class AdminOrdersSortController extends AdminController{

	public $combinaisons=null;
	public $exclusions=null;
	
	
	public $model;
	public function __construct(){
       $this->bootstrap = true;
       
        
        
		//$this->addRowAction('view');
        parent::__construct();
	}

	public function getTemplatePath()
	{ 
		return dirname(__FILE__).'/../../views/templates/admin/';
	}


	public function createTemplate($tpl_name) {
      	if (file_exists($this->getTemplatePath() . $tpl_name) && $this->viewAccess())
           return $this->context->smarty->createTemplate($this->getTemplatePath() . $tpl_name, $this->context->smarty);
           return parent::createTemplate($tpl_name);
    }

	public function initContent(){
		
		if(isset($_GET['action']) && $_GET['action']!=null && $_GET['action']!=""){
			$action=$_GET['action'];
			if($action=="populatetwistylist"){
				$this->populatetwistylist();
			}else if($action=="gettwistylist"){
				$this->gettwistylist();
			}else if($action=="removetwistylist"){
				$this->removetwistylist();
			}else if($action=="savetodb"){
				$this->savetodb();
			}else if($action=="removefinishedorders"){
				$this->removefinishedorders();
			}else if($action=="resetOrder"){
				$this->resetOrder();
			}else if($action=="removeoutofstockorders"){
				$this->removeoutofstockorders();
			}else if($action=="removeoutofstockorder"){
				$this->removeoutofstockorder();
			}
			
			
			/*else if($action=="changeorderstate"){
				$id_order=$_GET['id_order'];
				$this->changeorderstate($id_order);
			}*/
			
			exit;
		}
		
		$smarty = $this->context->smarty;
		//$smarty->assign('ord', OrderModel::getOrderDetail());
		
		
		
		//$smarty->assign('ord',OrdersModel::getOrdersDetail(Configuration::get('twisty_stat')));
		$smarty->assign('contr_link',$this->context->link->getAdminLink('AdminOrdersSort'));
		
		//$smarty->assign('base_url',str_replace('\\','/',dirname(__FILE__).'\\..\\..\\') );
		
		$smarty->assign('base_url',_MODULE_DIR_ .'twisty/'  );
		
		$this->content=$this->createTemplate('twisty.tpl')->fetch();
		parent::initContent();
	}

	
	  
  	
	
	public function searchBoxName($arr,$boxName){
		
		$res=false;
		for($i=0;$i<count($arr);$i++){
			if($arr['id_box']==$boxName){
				$res=true;
				break;
			}
		}
		return $res;
	}
	
	public function in_preBoxes_array($needle,$array){
		$res=false;
		$sec_array=array_values($array);
		if(in_array($needle,$sec_array))
			$res=true;
		return $res;
	}
	
	public function getNextBoxName($id_order,&$preBoxes,$listOccupedBoxes){
		if($this->combinaisons==null || $this->exclusions==null ){
			$arrayCombinaison=new arrayCombinaison();
			if($this->combinaisons==null)
				$this->combinaisons=$arrayCombinaison->getCombinaisons();
			if($this->exclusions==null)
				$this->exclusions=$arrayCombinaison->getExclusions();
		}
		
		//print_r($this->combinaisons);
		//print_r($this->exclusions);
		$boxname="";
		
		//XXXXXXXXXXXXXXXXXXXXXXXXXXXX
		if(array_key_exists($id_order,$preBoxes)){
			$boxname=$preBoxes[$id_order];
		}else if(array_key_exists($id_order,$listOccupedBoxes)){
			$boxname=$listOccupedBoxes[$id_order];
		//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
		}else{
			for($i=0;$i<count($this->combinaisons);$i++){
				if(!in_array($this->combinaisons[$i],$listOccupedBoxes) 
					&& !$this->in_preBoxes_array($this->combinaisons[$i],$preBoxes)
					&& !in_array($this->combinaisons[$i],$this->exclusions)){
					$boxname=$this->combinaisons[$i];
					$preBoxes[$id_order]=$this->combinaisons[$i];
					break;
				}
			}
		}
		return $boxname;
	}
	public function diff($twistyOrders,$orders){
		
		foreach($twistyOrders as $twistyOrder){
			$c=true;
			while($c){
				$c=false;
				for($i=0;$i<count($orders);$i++){
					
					//echo "-".$orders[$i]['id_order_detail']."-".$twistyOrder['id_order_detail']." - ".count($orders)."\n";
					if($twistyOrder['id_order_detail']==$orders[$i]['id_order_detail']){
						//echo "unset".$orders[$i]['id_order_detail']."\n";
						unset($orders[$i]);
						
						$orders=array_values($orders);
						
						//print_r($orders);
						//echo "\n ----------------- \n";
						$c=true;
						break;
					}
				}
			}
		}	
		//print_r($orders);
		
		return $orders;
	}
	public function getOccupedBoxes($twistyOrders){
		$res=array();
		foreach($twistyOrders as $order){
			if(array_search($order['id_box'],$res)==null){
			//if(!array_key_exists($order['id_order']){
				$res[$order['id_order']]=$order['id_box'];
			}
		}
		return $res;
	}
   	public function populatetwistylist(){
		
		$twistyOrders=OrdersModel::getTwistyOrdersDetailRows();
		$orders=OrdersModel::getOrdersDetail(Configuration::get('twisty_stat'));
		$this->syncTwistyAndOrdersTables($twistyOrders,$orders);
		
		
		$PreBoxes=array();
		
		//$rr="";
		
		$twistyOrders=OrdersModel::getTwistyOrdersDetailRows();
		
		
		$listOccupedBoxes=$this->getOccupedBoxes($twistyOrders);
		
		
		$orders=OrdersModel::getOrdersDetail(Configuration::get('twisty_stat'));
		
		
		
		$ordersIds=$this->getOrdersIds($orders);
		
		//print_r($orders);
		/*echo "+--------+";
		print_r($twistyOrders);
		echo "+--------+";
		
		print_r($orders);
		echo "+--------+";
		echo "Success";*/
		foreach($ordersIds as $id){
			$res=$this->getTwistyOrderInfos($twistyOrders,$id);
			
			//echo $id."-".$res."<br/>";
			
			if($res==-1){
				//ADD TO TWISTY
				$orderRows=$this->getOrderRows($orders,$id);
				foreach($orderRows as $orderRow){
					$arr=array(
						'id_order_detail' => $orderRow['id_order_detail'],
						'qte_picked' => '0',
						'id_box' => $this->getNextBoxName($id,$preBoxes,$listOccupedBoxes),
					);
					//print_r($arr);
					OrdersModel::addTwistyOrder($arr);
				}
				
			}else if($res==1){
				echo 'Do nothing :/ \n'; 
				
			}else if($res==2){
				OrdersModel::desableTwistyOrder($id);
				$orderRows=$this->getOrderRows($orders,$id);
				foreach($orderRows as $orderRow){
					$arr=array(
						'id_order_detail' => $orderRow['id_order_detail'],
						'qte_picked' => '0',
						'id_box' => $this->getNextBoxName($id,$preBoxes,$listOccupedBoxes),
					);
					//print_r($arr);
					OrdersModel::addTwistyOrder($arr);
				}
				echo 'duplicate \n'; 
			}
		}
		
	}
	
	private function syncTwistyAndOrdersTables($twistyOrders,$orders){
		$todelete=array();
		foreach($twistyOrders as $twistyorder){
			$exist=false;
			foreach($orders as $order){
				if($order['id_order']==$twistyorder['id_order'])
					$exist=true;
			}
			if(!$exist)
				$todelete[]=$twistyorder['id_order'];
		}
		$todelete=array_unique($todelete);
		if(count($todelete)>0)
			OrdersModel::removeTwistyOrders($todelete);
	}
	
	public function getOrderRows($orders,$id){
		$res=array();
		foreach($orders as $orderRow){
			if($orderRow['id_order']==$id){
				$res[]=$orderRow;
			}
		}
		return $res;
	}
	public function getTwistyOrderInfos($twistyOrders,$id_order){
		$exist=false;
		foreach($twistyOrders as $order){
			if($order['id_order'] == $id_order){
				$exist=true;
			}
		}
		if(!$exist){
			return -1;
		}else{
			if($this->isOrderShow($twistyOrders,$id_order)){
				return 1;
			}else{
				return 2;				
			}
		}
	}
	public function isOrderShow($twistyOrders,$id_order){
		$show=true;
		foreach($twistyOrders as $order){
			if($order['id_order']==$id_order && $order['is_show']=='0'){
				$show=false;
			}
		}
		return $show;
	}
	
	public function getOrdersIds($orders){
		$res=array();
		foreach($orders as $order){
			$res[]=$order['id_order'];
		}
		return array_values(array_unique($res));
	}
	
	public function searchOrderInArray($orders,$value){
		$index=false;
	
		foreach($orders as $order){
			if($order['id_order_detail']==$value)
				$index=true;
		}
	
		return $index;
	}
	public function gettwistylist(){
		$orders=OrdersModel::getTwistyOrdersDetail();
		
		$model=array();
		
		foreach($orders as $order){
			//
			
			$mod_order=array();
			$mod_order['id_twisty']=$order['id_twisty'];
			$mod_order['qte_picked']=$order['qte_picked'];
			$mod_order['is_finished']=$order['is_finished'];
			$mod_order['id_box']=$order['id_box'];
			$mod_order['product_ean13']=$order['product_ean13'];
			$mod_order['product_quantity']=$order['product_quantity'];
			$mod_order['id_order']=$order['id_order'];
			$mod_order['product_name']=$order['product_name'];
			$mod_order['product_reference']=$order['product_reference'];
			
			$mod_order['payment']=$order['payment'];
			$mod_order['total_shipping']=$order['total_shipping'];
			$mod_order['total_paid_tax_incl']=$order['total_paid_tax_incl'];
			
			$model[]=$mod_order;
			//
		}
		
		echo json_encode($model);
	}
	
	public function removetwistylist(){
		if(OrdersModel::removetwistylist()){
			echo "removed";
		}else{
			echo "Erreur";
		}
	}
	
	public function savetodb(){
		
		//$history=json_decode($_POST['history']);
		///$list=json_decode($_POST['list']);
		
		$list=json_decode($_POST['list_json_copy']);
		$history=json_decode($_POST['history_json_copy']);
		
		/*print_r($history);
		echo '\n ---------------------- \n';
		print_r($list);
		*/
		
		
		$req="";
		//echo $list;
		foreach($list as $el){
			//echo $el->vars->id_twisty."\n";
			
			if( in_array($el->vars->id_twisty,$history) ){
				//echo "ok";
				$req.='UPDATE '._DB_PREFIX_.'twisty_orders SET qte_picked = \''.$el->vars->qte_picked.'\' WHERE id_twisty=\''.$el->vars->id_twisty.'\';';
			}
		}
	
		
		OrdersModel::executeRequette($req);
		//echo "\n";
		//echo $req;
		
		/*
		print_r( $_POST['tmp_json']);
		echo "\n---------------------------\n";
		print_r( $_POST['history_saved_copy']);
		*/
		/*
		echo "\n ----------------------- \n";
		print_r($principale);
		echo "\n ----------------------- \n";
		print_r($tmp);
		echo "\n ----------------------- \n";
		*/
		
	}
	public function removefinishedorders(){
		//echo "ok";
		$order_state_id=(int)Configuration::get('twisty_stat_to');
		/*
		print_r($_POST['list_orders_ids']);
		echo "<br/>---------------";
		print_r($_POST['list_twisty']);
		echo "<br/>---------------";
		*/
		//print_r($_POST['list_ids']);
		//die;
		if($_POST['list_orders_ids']){
			$list_orders_ids=json_decode($_POST['list_orders_ids']);
			foreach($list_orders_ids as $id){
				$objOrder = new Order($id);
				$objOrder->setCurrentState($order_state_id);
				OrdersModel::removeFinishedOrders($id);
			}
		}
		
		//print_r();
		/*if($_POST['list_ids']){
			$list_ids=json_decode($_POST['list_ids']);	
			//print_r($list_ids);
			echo OrdersModel::removeFinishedOrders($list_ids);
			
		}*/
		/*
		$order_state_id=(int)Configuration::get('state2_159357');
		//$objOrder = new Order((object)array('id'=>'$id_ord')); //order with id=$_GET["action"]
		$objOrder = new Order($id_ord);
		//$objOrder->getFields();
		//echo $id_ord."<br/>";
		//print_r($objOrder);
		//die();
		$objOrder->setCurrentState($order_state_id);
		*/		
		//echo OrdersModel::removeFinishedOrders();
	}
	/*public function changeorderstate($id_order){
		$order = new Order($id_order);
		$id_order_state=Configuration::get('twisty_stat_to');
		echo $id_order_state.','.$id_order;
		$order->setCurrentState($id_order_state);
	}*/
	public function removeoutofstockorders(){
		//print_r($_POST['list_orders_ids']);
		//die;
		$order_state_id=(int)Configuration::get('twisty_stat_to2');
		if($_POST['list_orders_ids']){
			$list_orders_ids=json_decode($_POST['list_orders_ids']);
			foreach($list_orders_ids as $id){
				$objOrder = new Order($id);
				$objOrder->setCurrentState($order_state_id);
				OrdersModel::removeFinishedOrders($id);
			}
		}
	}
	public function removeoutofstockorder(){
		//print_r($_POST['list_orders_ids']);
		//die;
		//echo 
		$order_state_id=(int)Configuration::get('twisty_stat_to2');
		
		
		if($_POST['id']){
			
			$id=$_POST['id'];
		//	echo $id;
		//	die;
			//$list_orders_ids=json_decode($_POST['list_orders_ids']);
			//foreach($list_orders_ids as $id){
				$objOrder = new Order($id);
				$objOrder->setCurrentState($order_state_id);
				OrdersModel::removeFinishedOrders($id);
			//}
		}
	}
	
	public function resetOrder(){
		if(isset($_POST['id_order'])){
			$id_order=$_POST['id_order'];
			echo "Reset order: ".$id_order;
			OrdersModel::resetOrder($id_order);
		}
	}
	
}

