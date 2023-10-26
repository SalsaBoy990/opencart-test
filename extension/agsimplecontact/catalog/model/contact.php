<?php
namespace Opencart\Catalog\Model\Extension\Agsimplecontact;

/**
 * DB repository to manage contacts (submitted on the contact form)
 */
class Contact extends \Opencart\System\Engine\Model
{
    /**
     * Add one contact
     *
     * @param $data
     *
     * @return int
     */
    public function addContact($data): int
    {
        // gdpr checkbox
        if ( ! isset($data['gdpr'])) {
            $data['gdpr'] = 0;
        }

        $this->db->query("INSERT INTO ".DB_PREFIX."contacts SET name = '".$data['name']."', `email` = '".$data['email']."', `gdpr` = '".(int) $data['gdpr']."',`message` = '".$data['message']."'");

        return $this->db->getLastId();
    }

}

?>
