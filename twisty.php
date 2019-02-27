<?php

if (!defined('_PS_VERSION_')) 
  exit; 
  
require_once dirname(__FILE__).'/models/OrdersModel.php';
require_once dirname(__FILE__).'/classes/arrayCombinaison.php';

class twisty extends Module {
	  
	  
		
	protected $tabvalue1_7 = array(
		array(
			'class_name' => 'AdminOrdersSort',
			'id_parent' => -1,
			'module' => 'twisty',
			'name' => 'Twisty',
			'active'    => 1
		)
	);

	public $all_tabs = array(
		array(
	        'class_name' => 'Adminxippost',
	        'id_parent' => 'parent',
	        'name' => 'Blog Posts',
		)
	);
	public static $ModuleName = 'twisty';
	
	protected $tabs = [
        [
            'name'      => 'Twisty',
            'className' => 'AdminOrdersSort',
            'active'    => 1
        ],
    ];
	
	public function __construct() {
		$this->name = 'twisty';
		$this->displayName = 'Twisty';
		$this->tab = 'Administration';
		$this->ps_versions_compliancy=['min'=>'1.6','max'=>_PS_VERSION_];
		$this->version = '1.0';
		$this->author = 'ERRAMY NOUREDDINE';
		$this->description = 'Twisty is Prestashop module helps E-commerce agents to sort items by order in Warehouse. No wasting time and Energy :) ';
		$this->bootstrap=true;
		
		
		
		parent::__construct();
		
	}
	public function loadSQLFile($sql_file)
	{
	  // Get install SQL file content
	  $sql_content = file_get_contents($sql_file);

	  // Replace prefix and store SQL command in array
	  $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
	  $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);

	  // Execute each SQL statement
	  $result = true;
	  foreach($sql_requests as $request)
	  if (!empty($request))
		$result &= Db::getInstance()->execute(trim($request));

	  // Return result
	  return $result;
	}
	public function install()
	{
		
		if (!parent::install() 
		|| !$this->registerHook('actionAdminControllerSetMedia') 
		|| !$this->registerHook('displayBackOfficeHeader') 
		|| !$this->registerHook('header') 
		)
        return false;
		
		$sql_file = dirname(__FILE__).'/install/install.sql';
		if (!$this->loadSQLFile($sql_file))
			return false;	
		
		
		$vrs=substr(_PS_VERSION_,0,3);
		if($vrs=='1.7'){
			$this->Register_Tabs() ;
		}else{
			
			$this->addTab($this->tabs);
		}
		
		
		
		return true;
	}
	
	public function uninstall()
	{
		//$this->removeTab($this->tabs);
		$vrs=substr(_PS_VERSION_,0,3);
		if($vrs=='1.7'){
			$this->UnRegister_Tabs();
		}else{
			$this->removeTab($this->tabs);
		}
		
		$this->unregisterHook('displayBackOfficeHeader');
		$this->unregisterHook('actionAdminControllerSetMedia');
		$this->unregisterHook('header');
		
		$sql_file = dirname(__FILE__).'/install/uninstall.sql';
	    if (!$this->loadSQLFile($sql_file) )
			return false;

		
		return parent::uninstall();
	}
	public function addTab($tabs,$id_parent = 0)
    {
        foreach ($tabs as $tab)
        {
            $tabModel             = new Tab();
            $tabModel->module     = $this->name;
            $tabModel->active     = $tab['active'];
            $tabModel->class_name = $tab['className'];
            $tabModel->id_parent  = $id_parent;

            //tab text in each language
            foreach (Language::getLanguages(true) as $lang)
            {
                $tabModel->name[$lang['id_lang']] = $tab['name'];
            }

            $tabModel->add();

            //submenus of the tab
            if (isset($tab['childs']) && is_array($tab['childs']))
            {
                $this->addTab($tab['childs'], Tab::getIdFromClassName($tab['className']));
            }
        }
        return true;
    }

    public function removeTab($tabs)
    {
        foreach ($tabs as $tab)
        {
            $id_tab = (int) Tab::getIdFromClassName($tab["className"]);
            if ($id_tab)
            {
                $tabModel = new Tab($id_tab);
                $tabModel->delete();
            }

            if (isset($tab["childs"]) && is_array($tab["childs"]))
            {
                $this->removeTab($tab["childs"]);
            }
        }

        return true;
    }
	public function valide($varname,$type){
		if(!empty($varname))
		if($type=="INT"){
			$s=Validate::isINT($varname);
			if(!$s)
				return false;
		}
		return true;
	}
	public function getContent(){
		$ok=true;
		$output="";
		
		if (Tools::isSubmit('submit'.$this->name)){
			
			//print_r(Tools::getValue('twisty_stat'));
			//$s=implode(",",Tools::getValue('twisty_stat'));
			//echo $s;
			
			
			//if(!$this->valide(strval(Tools::getValue('twisty_stat')),'INT')){
				//$ok=false;
				//$output .= $this->displayError('Erreur!');
			//}
			if($ok){
				//$s=;
				Configuration::updateValue('twisty_stat', strval(implode(",",Tools::getValue('twisty_stat'))));
				Configuration::updateValue('twisty_stat_to', strval(Tools::getValue('twisty_stat_to')));
				Configuration::updateValue('twisty_stat_to2', strval(Tools::getValue('twisty_stat_to2')));
				Configuration::updateValue('twisty_format', strval(Tools::getValue('twisty_format')));
				Configuration::updateValue('twisty_exclu', strval(Tools::getValue('twisty_exclu')));
				
				$output .= $this->displayConfirmation('Settings updated');
			}
		}
		
		return $output.$this->displayForm();
	}
	public function displayForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$res=OrdersModel::getAllStates($default_lang);
		$def1=Configuration::get('twisty_stat');
		$def2=Configuration::get('twisty_stat_to');
		$def3=Configuration::get('twisty_stat_to2');
		
		$default1=null;
		$options1=null;
		$default2=null;
		$options2=null;
		$default3=null;
		$options3=null;
		
		foreach($res as $row){
			
			/*if($def1==$row['id_order_state']){
				
				$default1=array(
					'value' => $row['id_order_state'],                 // The value of the 'value' attribute of the <option> tag.
					'label' => $row['name']   ,
					
				  );
			}else{*/
				$options1[]=array(
					'id_option' => $row['id_order_state'],                 // The value of the 'value' attribute of the <option> tag.
					'name' => $row['name']   ,
					
				  );
			//}
			
			if($def2==$row['id_order_state']){
				
				$default2=array(
					'value' => $row['id_order_state'],                 // The value of the 'value' attribute of the <option> tag.
					'label' => $row['name']   ,
					
				  );
			}else{
				$options2[]=array(
					'id_option' => $row['id_order_state'],                 // The value of the 'value' attribute of the <option> tag.
					'name' => $row['name']   ,
					
				  );
			}
			
			
			if($def3==$row['id_order_state']){
				
				$default3=array(
					'value' => $row['id_order_state'],                 // The value of the 'value' attribute of the <option> tag.
					'label' => $row['name']   ,
					
				  );
			}else{
				$options3[]=array(
					'id_option' => $row['id_order_state'],                 // The value of the 'value' attribute of the <option> tag.
					'name' => $row['name']   ,
					
				  );
			}
		}
		
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			
			
			'input' => array(
				array(
				  'type' => 'select',                              
				  'label' => $this->l('Status des commandes à traiter'),         
				  'desc' => $this->l(''),
				  'name' => 'twisty_stat[]',
				  'multiple' => true,
				  'required' => false,                            
				  'options' => array(
					'query' => $options1,                         
					'id' => 'id_option',                         
					'name' => 'name'
				  )
				),
				array(
				  'type' => 'select',                              
				  'label' => $this->l('Status des commandes terminées'),         
				  'desc' => $this->l(''),
				  'name' => 'twisty_stat_to',
				  'required' => false,                            
				  'options' => array(
					'query' => $options2,                         
					'id' => 'id_option',                         
					'name' => 'name',
					'default' => $default2					
				  )
				),
				array(
				  'type' => 'select',                              
				  'label' => $this->l('Status des commandes Out of Stock'),         
				  'desc' => $this->l(''),
				  'name' => 'twisty_stat_to2',
				  'required' => false,                            
				  'options' => array(
					'query' => $options3,                         
					'id' => 'id_option',                         
					'name' => 'name',
					'default' => $default3					
				  )
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('FORMAT'),
					'name' => 'twisty_format',
					'size' => 60,
					'required' => false
				),
				array(
					'type' => 'text',
					'label' => $this->l('Exclu'),
					'name' => 'twisty_exclu',
					'size' => 20,
					'required' => false
				)
			),
			
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		 
		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		 
		// Load current value
		//$helper->fields_value['twisty_stat']   = Configuration::get('twisty_stat');
		$helper->fields_value['twisty_stat[]']   = explode(",",Configuration::get('twisty_stat'));//array('3','15');
		$helper->fields_value['twisty_stat_to']   = Configuration::get('twisty_stat_to');
		$helper->fields_value['twisty_stat_to2']   = Configuration::get('twisty_stat_to2');
		$helper->fields_value['twisty_format'] = Configuration::get('twisty_format');
		$helper->fields_value['twisty_exclu']  = Configuration::get('twisty_exclu');
		
		
		
		return $helper->generateForm($fields_form);
	}
	public function hookDisplayBackOfficeHeader($params)
	{
	  // echo "<script>console.log( 'hookDisplayBackOfficeHeader' );</script>";
	   //echo "<script>console.log( '".($this->MODULE_DIR) . 'css/tab.css'."' );</script>";
		$this->context->controller->addCSS($this->_path . 'css/tab.css');
		$vrs=substr(_PS_VERSION_,0,3);
		if($vrs=='1.7'){
			$this->context->controller->addCSS($this->_path . 'css/style1_7.css');
		}
	}
	public function hookActionAdminControllerSetMedia($params)
	{
		//echo "<script>console.log( 'hookActionAdminControllerSetMedia' );</script>";
		if ($this->context->controller->controller_name == 'AdminOrdersSort'){ 
			//$this->context->controller->addCSS(($this->_path) . 'css/bootstrap-tagsinput.css');	
			//$this->context->controller->addJS(($this->_path) . 'js/bootstrap-checkbox.js');	
			
			$this->context->controller->addJS(($this->_path) . 'js/jquery.cookie.js');
			$this->context->controller->addJS(($this->_path) . 'js/script.js');
			//$this->context->controller->addJS(($this->_path) . 'js/classes.js');
			

		}
    }
	public function hookHeader(){
				//echo "<script>console.log( 'hookHeader' );</script>";

		//$this->context->controller->addCss(($this->_path).'css/tab.css');
	}
	
	
	public function Register_Tabs()
	{
		$tabs_lists = array();
        $langs = Language::getLanguages();
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $save_tab_id = $this->Register_ETabs();
    	/*if(isset($this->all_tabs) && !empty($this->all_tabs)){
    		foreach ($this->all_tabs as $tab_list)
    		{
    		    $tab_listobj = new Tab();
    		    $tab_listobj->class_name = $tab_list['class_name'];
    		    $tab_listobj->id_parent = $save_tab_id;
    		    if(isset($tab_list['module']) && !empty($tab_list['module'])){
    		    	$tab_listobj->module = $tab_list['module'];
    		    }else{
    		    	$tab_listobj->module = $this->name;
    		    }
    		    foreach($langs as $l)
    		    {
    		    	$tab_listobj->name[$l['id_lang']] = $this->l($tab_list['name']);
    		    }
    		    $tab_listobj->save();
    		}
    	}*/
        return true;
    }
	public function Register_ETabs(){
		$tabpar_listobj = new Tab();
		$langs = Language::getLanguages();
		$id_parent = (int)Tab::getIdFromClassName("IMPROVE");
		$tabpar_listobj->class_name = 'AdminOrdersSort';
		$tabpar_listobj->id_parent = $id_parent;
		$tabpar_listobj->module = $this->name;
		foreach($langs as $l)
	    {
	    	$tabpar_listobj->name[$l['id_lang']] = "Twisty";
	    }
	    if($tabpar_listobj->save()){
	    	return (int)$tabpar_listobj->id;
	    }else{
	    	return (int)$id_parent;
	    }
	}
	public function UnRegister_Tabs()
	{
		/*if(isset($this->all_tabs) && !empty($this->all_tabs)){
			foreach($this->all_tabs as $tab_list){
				$tab_list_id = Tab::getIdFromClassName($tab_list['class_name']);
			    if(isset($tab_list_id) && !empty($tab_list_id)){
			        $tabobj = new Tab($tab_list_id);
			        $tabobj->delete();
			    }
			}
		}*/
		$tabp_list_id = Tab::getIdFromClassName('AdminOrdersSort');
		$tabpobj = new Tab($tabp_list_id);
	    $tabpobj->delete();
        return true;
	}
}
