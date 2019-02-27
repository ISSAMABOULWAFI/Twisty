<?php 

class twistyMainModuleFrontController extends ModuleFrontController
{
	//const FILENAME = 'twistyMain';
	
	
	public function __construct()
	{
	    parent::__construct();
	    //$this->controller_type = 'modulefront';
	}
	public function init()
	{
		
	    parent::init();
		
	}
	public function initContent()
	{
		//$this->content=$this->createTemplate('main.tpl')->fetch();
		parent::initContent();
		//$this->setTemplate('module:twisty/views/templates/front/main.tpl');
		
		//$datas=OrdersModel::getTwistyListForFront();
		$smarty=$this->context->smarty;
		
		$smarty->assign('ord', "1111");
		
		//print_r($datas);
		//die;
        $this->setTemplate(sprintf('module:%s/views/templates/front/maintemplate.tpl', $this->module->name));
	}
	
	
	public function postProcess()
    {
		//echo "ok";
	}
	/*public function createTemplate($tpl_name) {
      	if (file_exists($this->getTemplatePath() . $tpl_name) && $this->viewAccess())
           return $this->context->smarty->createTemplate($this->getTemplatePath() . $tpl_name, $this->context->smarty);
           return parent::createTemplate($tpl_name);
    }*/
	/*public function getTemplatePath()
	{ 
		return dirname(__FILE__).'/../../views/templates/front/';
	}*/
}