<?php
namespace Opencart\Catalog\Controller\Extension\Tmdcustomheadermenu\Tmd;
class Tmdheader extends \Opencart\System\Engine\Controller {
	
	public function index() {
	
		$this->load->language('extension/tmdcustomheadermenu/tmd/tmdheader');
		
		$data['text_all'] = $this->language->get('text_all');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = [];

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = [];

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = [
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					];

					$children_data[] = [
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					];
				}

				// Level 1
				$data['categories'][] = [
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				];
			}
		}
		
		$data['headermenus'] = [];
		$this->load->model('extension/tmdcustomheadermenu/tmd/tmdheader');
		$data['headermenu'] =$this->model_extension_tmdcustomheadermenu_tmd_tmdheader->getHeadermenu();
		
		return $this->load->view('extension/tmdcustomheadermenu/tmd/tmdheader', $data); 
	}
}
