<?php
namespace Opencart\Admin\Controller\Extension\Tmdcustomheadermenu\Module;
class Headermenu extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('extension/tmdcustomheadermenu/module/headermenu');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setTitle($this->language->get('heading_title1'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/tmdcustomheadermenu/module/headermenu', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/tmdcustomheadermenu/module/headermenu|save', 'user_token=' . $this->session->data['user_token']);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_headermenu_status'] = $this->config->get('module_headermenu_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/tmdcustomheadermenu/module/headermenu', $data));
	}

	public function save(): void {
		$this->load->language('extension/tmdcustomheadermenu/module/headermenu');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/tmdcustomheadermenu/module/headermenu')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_headermenu', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void {

		$this->load->model('setting/event');
		$this->load->model('user/user_group');
		
		// // Menu events
		// $this->model_setting_event->deleteEventByCode('tmd_module_headermenu');
		// $this->model_setting_event->addEvent('tmd_module_headermenu', 'TMD Custom Header Menu', 'admin/view/common/column_left/before','extension/tmdcustomheadermenu/module/headermenu|menu', true, 1);

		// menu event
		
		if(VERSION>= '4.0.2.0')
		{
			$eventaction='extension/tmdcustomheadermenu/module/headermenu.menu';
		}
		else{
			$eventaction='extension/tmdcustomheadermenu/module/headermenu|menu';
		}
		$this->model_setting_event->deleteEventByCode('tmd_module_headermenu');
		
		$eventrequest=[
					'code'=>'tmd_module_headermenu',
					'description'=>'TMD Custom Header Menu',
					'trigger'=>'admin/view/common/column_left/before',
					'action'=>$eventaction,
					'status'=>'1',
					'sort_order'=>'1',
				];
		if(VERSION=='4.0.0.0')
		{
			$this->model_setting_event->addEvent('tmd_module_headermenu', 'TMD Custom Header Menu', 'admin/view/common/column_left/before','extension/tmdcustomheadermenu/module/headermenu|menu', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

		
		$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		$this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->install();

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		

		// Front Menu events 
		if(VERSION>= '4.0.2.0')
		{
			$eventaction='extension/tmdcustomheadermenu/module/headermenu.commoncontroller';
		}
		else{
			$eventaction='extension/tmdcustomheadermenu/module/headermenu|commoncontroller';
		}
		$this->model_setting_event->deleteEventByCode('tmd_frontheadermenu');
		
		$eventrequest=[
					'code'=>'tmd_frontheadermenu',
					'description'=>'TMD Front Custom Header Menu',
					'trigger'=>'catalog/view/common/header/before',
					'action'=>$eventaction,
					'status'=>'1',
					'sort_order'=>'1',
				];
		if(VERSION=='4.0.0.0')
		{
			$this->model_setting_event->addEvent('tmd_frontheadermenu', 'TMD Front Custom Header Menu', 'catalog/view/common/header/before','extension/tmdcustomheadermenu/module/headermenu|commoncontroller', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}

	}
	
	public function menu(string &$route, array &$args, mixed &$output): void {
		$modulestatus=$this->config->get('module_headermenu_status');
		if(!empty($modulestatus)){
		$this->load->language('extension/tmdcustomheadermenu/module/headermenu');
		
		$tmdcustomheadermenu = [];
			
				if ($this->user->hasPermission('access', 'extension/tmdcustomheadermenu/tmd/tmdcustomheader')) {
				$tmdcustomheadermenu[] = [
					'name'	   => $this->language->get('text_headermenu'),
					'href'     => $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader', 'user_token=' . $this->session->data['user_token']),
					'children' => []		
				];
				}
			
				if ($tmdcustomheadermenu) {					
				$args['menus'][] = [
					'id'       => 'menu-extension',
					'icon'	   => 'fas fa-tablet', 
					'name'	   => $this->language->get('text_tmdcustomheadermenu'),
					'href'     => '',
					'children' => $tmdcustomheadermenu
				];	
			}
	
	}
	}
	
	public function uninstall(): void {
		$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		$this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->uninstall();
		
		// Register events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('tmd_module_headermenu');
		
		// Fix permissions
		$this->load->model('user/user_group');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/tmdcustomheadermenu/module/headermenu');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/tmdcustomheadermenu/module/headermenu');
		
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/tmdcustomheadermenu/tmd/tmdcustomheader');
	}

}