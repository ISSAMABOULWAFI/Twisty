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

class arrayCombinaison
{
	private $arr=array();
	private $arrIndex=array();
	private $coef=1;
	private $comb=array();
	
	public function __construct() {
		$this->arr=explode(';',Configuration::get('twisty_format'));
		$this->arrIndex=$this->initIndexesArray();
		$this->coef=$this->initCoef();
		//$comb=getCombinaisons($coef,$arr,$arrIndex);

		
	}
	private function initIndexesArray(){
		$arrIndex=array();
		$count=count($this->arr);
		for($i=0;$i<$count;$i++){
			$arrIndex[]=0;
		}
		return $arrIndex;
	}
	private function initCoef(){
		$coef=1;
		$count=count($this->arr);
		for($i=0;$i<$count;$i++){
			$coef*=strlen($this->arr[$i]);
		}
		return $coef;
	}
	
	
	private function getNext(){
		
		for($i=count($this->arrIndex)-1;$i>=0;$i--){
			if($this->arrIndex[$i]<(strlen($this->arr[$i])-1)){
				$this->arrIndex[$i]++;
				break;
			}else{
				$this->arrIndex[$i]=0;
			}
		}
		//return $this->arrIndex;
	}


	private function getBoxName(){
		$boxName="";
		for($i=0;$i<count($this->arr);$i++){
			$boxName.=$this->arr[$i][$this->arrIndex[$i]];
		}
		return $boxName;
	}
	public function getCombinaisons(){
		$comb=array();
		for($i=0;$i<$this->coef;$i++) {
			//print_r( $arrIndex);
			//echo "<br/>";
			//echo strrev(getBoxName($arr,$arrIndex))."<br/>";	
			$comb[]=$this->getBoxName();	
			$this->getNext();	
			//
		}
		return $comb;
	}
	public function getExclusions(){
		
		return explode(',',Configuration::get('twisty_exclu'));
	}
	
	
	  
	
}

