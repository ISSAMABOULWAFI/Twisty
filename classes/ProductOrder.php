<?php
/**
* 2007-2016 PrestaShop.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductOrder extends Order
{

	//get all orders 	
	
	public function getAllProductOrders()
	{
	
	    $id_lang = (int)Context::getContext()->language->id; 
		$dbquery = new DbQuery();
		$dbquery->select('od.`id_order` AS `id_order`, od.`product_id` AS `id`, od.`product_name` AS `name`, pl.`id_product`');
		$dbquery->from('order_detail', 'od');
		
		$dbquery->leftJoin('product_lang', 'pl', 'pl.id_product = od.product_id AND pl.id_lang = '.$id_lang);			
		
		$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbquery->build());		

		return $orders;
		
	}	
	
	
	//get the orders for the current product
	
	public static function getProductOrders(){
		$id_lang=(int)Context::getContext()->language->id; 
		if(Tools::getValue('id')){
			$id_product=Tools::getValue('id');	
			$sql = 'SELECT  * FROM `'._DB_PREFIX_.'order_detail` od
			LEFT JOIN  `'._DB_PREFIX_.'product_lang` pl ON pl.`id_product` = od.`product_id`	
			WHERE  od.`product_id`='.$id_product;
			$results = Db::getInstance()->ExecuteS($sql);
			
			return $results;	
		}
	}
	  
	  
	
}

