<?php
namespace Opencart\Admin\Controller\Extension\Helloworld\Module;
class Helloworld extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/module/hello_world');


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/helloworld', $data));
	}

	protected function validate() {

	}


    public function install() {
        // Fix permissions
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/helloworld/module/helloworld');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/helloworld/module/helloworld');
    }

    public function uninstall() {
        $this->load->model('extension/helloworld/module/helloworld');
        $this->model_extension_helloworld_module_helloworld->uninstall();

        // Fix permissions
        $this->load->model('user/user_group');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/helloworld/module/helloworld');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/helloworld/module/helloworld');

    }
}
