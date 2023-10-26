<?php

namespace Opencart\Admin\Controller\Extension\Agsimplecontact\Module;

class Simplecontact extends \Opencart\System\Engine\Controller
{
    /* Translations */
    const TRANSLATION = 'extension/agsimplecontact/module/simplecontact';

    /* Permissions */
    const PERMISSION = 'extension/agsimplecontact/ag/agsimplecontact';


    /* Routes */
    const BASE_URL = 'extension/agsimplecontact/module/simplecontact';
    const LIST_URL = 'extension/agsimplecontact/ag/agsimplecontact|list';


    /* Views */
    const FRONT_VIEW = 'extension/agsimplecontact/module/setting';


    /* Models */
    const CONTACT_MODEL = 'extension/agsimplecontact/contact';


    /* Codes for events */
    const ADMIN_EVENT_CODE = 'agsimplecontact_module_simplecontact';
    const CATALOG_EVENT_CODE = 'agsimplecontact_catalog_simplecontact';


    private $error = array();


    /**
     * Frontpage of the extension
     *
     * @return void
     */
    public function index(): void
    {
        $this->load->language(self::TRANSLATION);
        $this->load->model(self::CONTACT_MODEL);

        $this->document->setTitle($this->language->get('heading_title'));

        /* Breadcrumbs */
        $data['breadcrumbs']   = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token='.$this->session->data['user_token']),
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/agsimplecontact/module/simplecontact',
                'user_token='.$this->session->data['user_token']),
        ];


        $data['save']   = $this->url->link('extension/agsimplecontact/module/simplecontact|save',
            'user_token='.$this->session->data['user_token']);
        $data['cancel'] = $this->url->link('marketplace/extension',
            'user_token='.$this->session->data['user_token'].'&type=module');

        $data['module_simplecontact_status'] = $this->config->get('module_simplecontact_status');


        /* Common template parts */
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view(self::FRONT_VIEW, $data));
    }


    /**
     * Persist one new/updated contact into db
     *
     * @return void
     */
    public function save(): void
    {
        $this->load->language(self::TRANSLATION);

        $json = [];

        if ( ! $this->user->hasPermission('modify', self::PERMISSION)) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        if ( ! $json) {
            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting('module_simplecontact', $this->request->post);

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
     * Runs when installing the extension
     *
     * @return void
     */
    public function install(): void
    {
        /* Load models */
        $this->load->model('setting/event');
        $this->load->model('user/user_group');
        $this->load->model(self::CONTACT_MODEL);


        /* Add table, seed table */
        $this->model_extension_agsimplecontact_contact->install();
        $this->model_extension_agsimplecontact_contact->seed();


        /* Setup event for the menu */
        if (VERSION >= '4.0.2.0') {
            // action to be called...
            $eventAction = self::BASE_URL.'.menu';
        } else {
            $eventAction = self::BASE_URL.'|menu';
        }
        // delete previous
        $this->model_setting_event->deleteEventByCode(self::ADMIN_EVENT_CODE);

        $eventRequest = [
            'code'        => self::ADMIN_EVENT_CODE,
            'description' => 'AG Simple Contacts Extension',
            'trigger'     => 'admin/view/common/column_left/before',
            'action'      => $eventAction,
            'status'      => '1',
            'sort_order'  => '1',
        ];

        if (VERSION == '4.0.0.0') {
            $this->model_setting_event->addEvent(self::ADMIN_EVENT_CODE, 'AG Simple Contacts Extension',
                'admin/view/common/column_left/before', 'extension/agsimplecontact/module/simplecontact|menu', true,
                1);
        } else {
            $this->model_setting_event->addEvent($eventRequest);
        }


        // Add permissions for access
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', self::PERMISSION);
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', self::PERMISSION);


        // Public Contact Form events
        if (VERSION >= '4.0.2.0') {
            $eventAction = 'extension/agsimplecontact/module/simplecontactform.commoncontroller';
        } else {
            $eventAction = 'extension/agsimplecontact/module/simplecontactform|commoncontroller';
        }
        $this->model_setting_event->deleteEventByCode(self::CATALOG_EVENT_CODE);

        $eventRequest = [
            'code'        => self::CATALOG_EVENT_CODE,
            'description' => 'AG Simple Contacts Public Form',
            'trigger'     => 'catalog/view/common/header/before',
            'action'      => $eventAction,
            'status'      => '1',
            'sort_order'  => '1',
        ];
        if (VERSION == '4.0.0.0') {
            $this->model_setting_event->addEvent(self::CATALOG_EVENT_CODE, 'AG Simple Contacts Public Form',
                'catalog/view/common/header/before', self::BASE_URL.'|commoncontroller',
                true, 1);
        } else {
            $this->model_setting_event->addEvent($eventRequest);
        }
    }


    /**
     * Runs when uninstalling the extension
     *
     * @return void
     */
    public function uninstall(): void
    {
        $this->load->model('user/user_group');
        $this->load->model('setting/event');
        $this->load->model(self::CONTACT_MODEL);


        /* Delete custom table */
        $this->model_extension_agsimplecontact_contact->uninstall();


        /* Delete menu */
        $this->model_setting_event->deleteEventByCode(self::ADMIN_EVENT_CODE);
        $this->model_setting_event->deleteEventByCode(self::CATALOG_EVENT_CODE);


        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access',
            'extension/agsimplecontact/module/simplecontact');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify',
            'extension/agsimplecontact/module/simplecontact');


        /* Delete permissions */
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', self::PERMISSION);
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', self::PERMISSION);
    }


    /**
     * Admin sidebar navigation menu -> add links
     *
     * @param  string  $route
     * @param  array  $args
     * @param  mixed  $output
     *
     * @return void
     */
    public function menu(string &$route, array &$args, mixed &$output): void
    {
        $moduleStatus = $this->config->get('module_agsimplecontact_status');

        if (empty($moduleStatus)) {
            $this->load->language(self::TRANSLATION);

            $customMenu = [];

            if ($this->user->hasPermission('access', self::PERMISSION)) {
                $customMenu[] = [
                    'name'     => 'Module Settings',
                    'href'     => $this->url->link(self::BASE_URL, 'user_token='.$this->session->data['user_token']),
                    'children' => [],
                ];
                $customMenu[] = [
                    'name'     => $this->language->get('text_headermenu'),
                    'href'     => $this->url->link(self::LIST_URL,
                        'user_token='.$this->session->data['user_token']),
                    'children' => [],
                ];
            }

            if ($customMenu) {
                $args['menus'][] = [
                    'id'       => 'menu-extension',
                    'icon'     => 'fas fa-message',
                    'name'     => $this->language->get('text_subheadermenu'),
                    'href'     => '',
                    'children' => $customMenu,
                ];
            }

        }
    }
}
