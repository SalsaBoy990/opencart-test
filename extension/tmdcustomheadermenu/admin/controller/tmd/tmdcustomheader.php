<?php
namespace Opencart\Admin\Controller\Extension\Tmdcustomheadermenu\Tmd;
class Tmdcustomheader extends \Opencart\System\Engine\Controller {
	
	public function index():void {
		
		$this->language->load('extension/tmdcustomheadermenu/tmd/tmdcustomheader');

		$this->document->setTitle($this->language->get('heading_title'));
		 
		$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');

		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader', 'user_token=' . $this->session->data['user_token'] . $url)
		];
		
		$data['add'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|form', 'user_token=' . $this->session->data['user_token'] . $url);
			
		$data['delete'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|delete', 'user_token=' . $this->session->data['user_token'] . $url);	
		
		$data['list'] = $this->getList();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/tmdcustomheadermenu/tmd/tmdcustomheader', $data));
	}
	
	public function list(): void {
		$this->load->language('catalog/manufacturer');

		$this->response->setOutput($this->getList());
	}
	
	protected function getList(): string {
		$this->load->language('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['action'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|list', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['headermenus'] = [];

		$data1 = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];
		
		$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		
		$headermenu_total = $this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->getTotalheadermenus();
	
		$results = $this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->getheadermenus($data1);
 
    	
		foreach ($results as $result) {
			$data['headermenus'][] = [
				'headermenu_id' => $result['headermenu_id'],
				'title'          => $result['title'],
				'level2'          => $result['level2'],
				'link'          => $result['link'],
				'sort_order'          => $result['sort_order'],
				'edit'       => $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|form', 'user_token=' . $this->session->data['user_token'] . '&headermenu_id=' . $result['headermenu_id'] . $url),
				'selected'       => isset($this->request->post['selected']) && in_array($result['headermenu_id'], $this->request->post['selected'])
			];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
	
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_link'] = $this->language->get('column_link');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');		
		
		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_delete'] = $this->language->get('button_delete');
 
 		$data['sort_title'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|list', 'user_token=' . $this->session->data['user_token'] . '&sort=td.title' . $url);

		$data['sort_sort_order'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|list', 'user_token=' . $this->session->data['user_token'] . '&sort=t.sort_order' . $url);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $headermenu_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($headermenu_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($headermenu_total - $this->config->get('config_pagination_admin'))) ? $headermenu_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $headermenu_total, ceil($headermenu_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		return $this->load->view('extension/tmdcustomheadermenu/tmd/headermenu_list', $data);		
	}
	
	
	public function form(): void {
		
		$this->load->language('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		
		$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['text_form'] = !isset($this->request->get['headermenu_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
  		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader', 'user_token=' . $this->session->data['user_token'] . $url)
		];
		
		$data['save'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader|save', 'user_token=' . $this->session->data['user_token']);
		
		$data['cancel'] = $this->url->link('extension/tmdcustomheadermenu/tmd/tmdcustomheader', 'user_token=' . $this->session->data['user_token'] . $url);
							
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$data['headermenu'] = $this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->getheadermenus();
		$data['headermenu1'] = $this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->getheadermenus1();
		$this->load->model('setting/store');
		
		$data['stores'] = $this->model_setting_store->getStores();
		
		if (isset($this->request->get['headermenu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$headermenu_info = $this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->getheadermenu($this->request->get['headermenu_id']);
		}
		if (isset($this->request->get['headermenu_id'])) {
			$data['headermenu_id'] = (int)$this->request->get['headermenu_id'];
		} else {
			$data['headermenu_id'] = 0;
		}
		
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($headermenu_info)) {
			$data['status'] = $headermenu_info['status'];
		} else {
			$data['status'] = 1;
		}	
		
		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($headermenu_info)) {
			$data['sort_order'] = $headermenu_info['sort_order'];
		} else {
			$data['sort_order'] ='';
		}
				
	
		///////////////////////headermenus/////////////////////
		if (isset($this->request->post['headermenu_description'])) {
			$data['headermenu_description'] = $this->request->post['headermenu_description'];
		} elseif (isset($this->request->get['headermenu_id'])) {
			$data['headermenu_description'] =$this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->getheadermenuDescriptions($this->request->get['headermenu_id']);
		} else {
			$data['headermenu_description'] = [];
		}
		
		
		
	
		if (isset($this->request->post['level1'])) {
			$data['level1'] = $this->request->post['level1'];
		} elseif (!empty($headermenu_info)) {
			$data['level1'] = $headermenu_info['level1'];
		} else {
			$data['level1'] = '';
		}
		
		
		if (isset($this->request->post['level2'])) {
			$data['level2'] = $this->request->post['level2'];
		} elseif (!empty($headermenu_info)) {
			$data['level2'] = $headermenu_info['level2'];
		} else {
			$data['level2'] = '';
		}
		
			if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($headermenu_info)) {
			$data['column'] = $headermenu_info['column'];
		} else {
			$data['column'] = '';
		}
		
		
		///////////////////////headermenus/////////////////////

		$this->load->model('design/layout');
		
		$data['layouts'] = $this->model_design_layout->getLayouts();
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('extension/tmdcustomheadermenu/tmd/headermenu_form', $data));
	}
	
	public function save(): void {
		$this->load->language('extension/tmdcustomheadermenu/tmd/tmdcustomheader');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/tmdcustomheadermenu/tmd/tmdcustomheader')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}
	
		

		if (!$json) {
			$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
			
			if (!$this->request->post['headermenu_id']) {
				$json['headermenu_id'] = $this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->addheadermenu($this->request->post);
			} else {
				$this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->editheadermenu($this->request->post['headermenu_id'], $this->request->post);
			}
			
			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
 
	public function delete(): void {
		
		$this->load->language('extension/tmdcustomheadermenu/tmd/tmdcustomheader');
		
		$json = [];
		
		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
			$json['error'] = $this->language->get('error_permission');
		}
			
		
		
		if (!$json) {
			
			$this->load->model('extension/tmdcustomheadermenu/tmd/tmdcustomheader');

			foreach ($selected as $headermenu_id) {
				$this->model_extension_tmdcustomheadermenu_tmd_tmdcustomheader->deleteheadermenu($headermenu_id);
			}

			$json['success'] = $this->language->get('text_success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
}
?>