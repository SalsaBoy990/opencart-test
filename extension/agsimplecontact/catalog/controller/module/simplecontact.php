<?php
namespace Opencart\Catalog\Controller\Extension\Agsimplecontact\Module;

class Simplecontact extends \Opencart\System\Engine\Controller
{
    /* Translations */
    const TRANSLATION = 'extension/agsimplecontact/module/simplecontact';

    /* Routes */
    const SAVE_URL = 'extension/agsimplecontact/module/simplecontact|save';

    /* Views */
    const FORM_VIEW = 'extension/agsimplecontact/module/form';

    /* Models */
    const CONTACT_MODEL = 'extension/agsimplecontact/contact';

    private $error = array();


    /**
     * Save/update one contact
     *
     * @return void
     */
    public function index()
    {
        $this->load->language(self::TRANSLATION);
        $this->load->model(self::CONTACT_MODEL);

        // Add or update
        $data['text_form'] = 'Contact Us';

        /* Endpoints */
        $data['save'] = $this->url->link(self::SAVE_URL);


        /* Contact name */
        $data['name']    = '';
        $data['email']   = '';
        $data['message'] = '';
        $data['gdpr']    = 0;

        return $this->load->view(self::FORM_VIEW, $data);
    }


    /**
     * Persist one new/updated contact into db
     *
     * @return void
     */
    public function save(): void
    {
        $this->load->language(self::TRANSLATION);

        $json = $this->validate();

        if ( ! $json) {
            $this->load->model(self::CONTACT_MODEL);

            // create
            $json['id'] = $this->model_extension_agsimplecontact_contact->addContact($this->request->post);

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
