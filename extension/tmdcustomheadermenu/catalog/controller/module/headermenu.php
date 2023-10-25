<?php
namespace Opencart\Catalog\Controller\Extension\Tmdcustomheadermenu\Module;
class Headermenu extends \Opencart\System\Engine\Controller {
	public function commoncontroller(string &$route, array &$args, mixed &$output): void {

	$modulestatus=$this->config->get('module_headermenu_status');
		if(!empty($modulestatus)){
			$args['menu'] = $this->load->controller('extension/tmdcustomheadermenu/tmd/tmdheader');
		}
	}	
}