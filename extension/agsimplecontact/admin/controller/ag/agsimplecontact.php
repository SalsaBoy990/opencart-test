<?php

namespace Opencart\Admin\Controller\Extension\Agsimplecontact\Ag;

class Agsimplecontact extends \Opencart\System\Engine\Controller
{
    /* Translations */
    const TRANSLATION = 'extension/agsimplecontact/module/simplecontact';

    /* Permissions */
    const PERMISSION = 'extension/agsimplecontact/ag/agsimplecontact';

    /* Routes */
    const BASE_URL = 'extension/agsimplecontact/ag/agsimplecontact';
    const LIST_URL = 'extension/agsimplecontact/ag/agsimplecontact|list';
    const CONTACT_URL = 'extension/agsimplecontact/ag/agsimplecontact|contact';
    const FORM_URL = 'extension/agsimplecontact/ag/agsimplecontact|form';
    const SAVE_URL = 'extension/agsimplecontact/ag/agsimplecontact|save';
    const DELETE_URL = 'extension/agsimplecontact/ag/agsimplecontact|delete';


    /* Views */
    const LIST_VIEW = 'extension/agsimplecontact/module/list';
    const SHOW_VIEW = 'extension/agsimplecontact/module/view';
    const FORM_VIEW = 'extension/agsimplecontact/module/form';


    /* Models */
    const CONTACT_MODEL = 'extension/agsimplecontact/contact';


    private $error = array();


    public function index(): void
    {

    }

    /**
     * Contact list page
     *
     * @return void
     */
    public function list(): void
    {
        $this->response->setOutput($this->getList());
    }


    /**
     * Get paginated contact list crud \ view
     *
     * @return mixed
     */
    public function getList()
    {
        $this->load->language(self::TRANSLATION);

        /* Init common template parts */
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'submitted_date';
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
            $url .= '&sort='.$this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order='.$this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page='.$this->request->get['page'];
        }

        $data['action'] = $this->url->link(self::LIST_URL,
            'user_token='.$this->session->data['user_token'].$url);

        $data['contacts'] = [];

        $params = [
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin'),
        ];

        $this->load->model(self::CONTACT_MODEL);

        $contactsTotal = $this->model_extension_agsimplecontact_contact->getTotalContacts();
        $results       = $this->model_extension_agsimplecontact_contact->getContacts($params);


        foreach ($results as $result) {
            $data['contacts'][] = [
                'id'             => $result['id'],
                'name'           => $result['name'],
                'email'          => $result['email'],
                'message'        => $result['message'],
                'gdpr'           => $result['gdpr'],
                'submitted_date' => $result['submitted_date'],
                'view_url'       => $this->url->link(self::CONTACT_URL,
                    'user_token='.$this->session->data['user_token'].'&id='.$result['id']),
                'edit_url'       => $this->url->link(self::FORM_URL,
                    'user_token='.$this->session->data['user_token'].'&id='.$result['id']),
                'selected'       => isset($this->request->post['selected']) && in_array($result['id'],
                        $this->request->post['selected']),
            ];
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page='.$this->request->get['page'];
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm']    = $this->language->get('text_confirm');

        $data['column_name']           = $this->language->get('column_name');
        $data['column_email']          = $this->language->get('column_email');
        $data['column_submitted_date'] = $this->language->get('column_submitted_date');
        $data['column_action']         = $this->language->get('column_action');

        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_delete'] = $this->language->get('button_delete');

        $data['sort_name'] = $this->url->link(self::LIST_URL,
            'user_token='.$this->session->data['user_token'].'&sort=name'.$url);

        $data['sort_email'] = $this->url->link(self::LIST_URL,
            'user_token='.$this->session->data['user_token'].'&sort=email'.$url);

        $data['sort_submitted_date'] = $this->url->link(self::LIST_URL,
            'user_token='.$this->session->data['user_token'].'&sort=submitted_date'.$url);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort='.$this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order='.$this->request->get['order'];
        }

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $contactsTotal,
            'page'  => $page,
            'limit' => $this->config->get('config_pagination_admin'),
            'url'   => $this->url->link(self::LIST_URL,
                'user_token='.$this->session->data['user_token'].$url.'&page={page}'),
        ]);

        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($contactsTotal) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0,
            ((($page - 1) * $this->config->get('config_pagination_admin')) > ($contactsTotal - $this->config->get('config_pagination_admin'))) ? $contactsTotal : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')),
            $contactsTotal, ceil($contactsTotal / $this->config->get('config_pagination_admin')));

        $data['sort']  = $sort;
        $data['order'] = $order;

        $data['add_url']    = $this->url->link(self::FORM_URL, 'user_token='.$this->session->data['user_token']);
        $data['delete_url'] = $this->url->link(self::DELETE_URL, 'user_token='.$this->session->data['user_token']);

        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        return $this->load->view(self::LIST_VIEW, $data);
    }


    /**
     * Show one contact
     *
     * @return void
     */
    public function contact(): void
    {
        /* Get translations for string placeholders */
        $this->load->language(self::TRANSLATION);

        /* Title */
        $this->document->setTitle($this->language->get('contact_show_heading_title'));

        /* Breadcrumbs */
        $data['breadcrumbs']   = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token='.$this->session->data['user_token'], true),
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('contact_list_heading_title'),
            'href' => $this->url->link(self::LIST_URL, 'user_token='.$this->session->data['user_token'], true),
        ];

        /* Back to the list page */
        $data['back_url'] = $this->url->link(self::LIST_URL, 'user_token='.$this->session->data['user_token']);

        /* Init common template parts */
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        /* Get one contact by id */
        $this->load->model(self::CONTACT_MODEL);
        $id              = intval($this->request->get['id']);
        $data['contact'] = $this->model_extension_agsimplecontact_contact->getContact($id);

        /* Render view with data */
        $this->response->setOutput($this->load->view(self::SHOW_VIEW, $data));
    }


    /**
     * Save/update one contact
     *
     * @return void
     */
    public function form(): void
    {
        $this->load->language(self::TRANSLATION);
        $this->load->model(self::CONTACT_MODEL);
        $this->load->model('localisation/language');

        $this->document->setTitle($this->language->get('heading_title'));

        // Add or update
        $data['text_form'] = ! isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $url               = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort='.$this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order='.$this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page='.$this->request->get['page'];
        }

        /* Breadcrumbs */
        $data['breadcrumbs']   = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token='.$this->session->data['user_token']),
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('contact_list_heading_title'),
            'href' => $this->url->link(self::LIST_URL, 'user_token='.$this->session->data['user_token'].$url),
        ];


        /* Endpoints */
        $data['save'] = $this->url->link(self::SAVE_URL,
            'user_token='.$this->session->data['user_token']);

        $data['cancel'] = $this->url->link(self::LIST_URL, 'user_token='.$this->session->data['user_token'].$url);

        $data['user_token'] = $this->session->data['user_token'];


        $this->load->model('setting/store');
        $data['stores'] = $this->model_setting_store->getStores();

        // Get contact if exists
        if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $contact = $this->model_extension_agsimplecontact_contact->getContact($this->request->get['id']);
        }
        if (isset($this->request->get['id'])) {
            $data['id'] = (int) $this->request->get['id'];
        } else {
            $data['id'] = 0;
        }

        /* Contact name */
        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif ( ! empty($contact)) {
            $data['name'] = $contact['name'];
        } else {
            $data['name'] = '';
        }

        /* Contact email */
        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif ( ! empty($contact)) {
            $data['email'] = $contact['email'];
        } else {
            $data['email'] = '';
        }

        /* Contact message */
        if (isset($this->request->post['message'])) {
            $data['message'] = $this->request->post['message'];
        } elseif ( ! empty($contact)) {
            $data['message'] = trim($contact['message']);
        } else {
            $data['message'] = '';
        }

        /* Contact GDPR */
        if (isset($this->request->post['gdpr'])) {
            $data['gdpr'] = $this->request->post['gdpr'];
        } elseif ( ! empty($contact)) {
            $data['gdpr'] = $contact['gdpr'];
        } else {
            $data['gdpr'] = 0;
        }


        $this->load->model('design/layout');
        $data['layouts']     = $this->model_design_layout->getLayouts();
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view(self::FORM_VIEW, $data));
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
        $json = $this->validate();


        if ( ! $json) {
            $this->load->model(self::CONTACT_MODEL);

            if ( ! $this->request->post['id']) {
                // create
                $json['id'] = $this->model_extension_agsimplecontact_contact->addContact($this->request->post);
            } else {
                // update
                $this->model_extension_agsimplecontact_contact->editContact($this->request->post['id'],
                    $this->request->post);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
     * Deletes one contact
     *
     * @return void
     */
    public function delete(): void
    {
        $this->load->language(self::TRANSLATION);
        $json = [];

        if (isset($this->request->post['selected'])) {
            $selected = $this->request->post['selected'];
        } else {
            $selected = [];
        }

        if ( ! $this->user->hasPermission('modify', self::PERMISSION)) {
            $json['error'] = $this->language->get('error_permission');
        }


        if ( ! $json) {
            $this->load->model(self::CONTACT_MODEL);

            foreach ($selected as $id) {
                $this->model_extension_agsimplecontact_contact->deleteContact($id);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


    /**
     * Validate form submission
     *
     * @return array
     */
    private function validate(): array
    {
        $json = [];
        if ( ! $this->user->hasPermission('modify', self::PERMISSION)) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        if ((oc_strlen($this->request->post['name']) < 3) || (oc_strlen($this->request->post['name']) > 32)) {
            $json['error']['name'] = $this->language->get('error_name');
        }

        if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $json['error']['email'] = $this->language->get('error_email');
        }

        if ((oc_strlen($this->request->post['message']) < 10) || (oc_strlen($this->request->post['message']) > 3000)) {
            $json['error']['message'] = $this->language->get('error_message');
        }

        return $json;
    }

}
