<?php
namespace Opencart\Admin\Model\Extension\Helloworld\Contact;

/**
 * DB repository to manage contacts (submitted on the contact form)
 */
class Contact extends \Opencart\System\Engine\Model
{

    /**
     * @return void
     */
    public function install(): void
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."contacts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `gdpr` TINYINT(1) NOT NULL DEFAULT '0',
  `submitted_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE 'utf8mb4_general_ci' AUTO_INCREMENT=1 ;");

    }


    /**
     * Seed the custom table with fake data
     *
     * @return void
     */
    public function seed(): void
    {

        /* Seed data */
        $message = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        $contacts   = [];
        $contacts[] = [
            'name'    => 'Adam Smith',
            'email'   => 'adam.smith@example.com',
            'message' => $message,
        ];
        $contacts[] = [
            'name'    => 'Trap Pista',
            'email'   => 'trap.pista@example.com',
            'message' => $message.' 2',
        ];
        $contacts[] = [
            'name'    => 'Klemen Tina',
            'email'   => 'klemen.tina@example.com',
            'message' => $message.' 3',
        ];

        foreach ($contacts as $contact) {
            $this->db->query("INSERT INTO `".DB_PREFIX."contacts` SET name = '".$contact['name']."', email = '".$contact['email']."', gdpr = '". 1 ."',message = '".$contact['message']."'");
        }
    }


    /**
     * @return void
     */
    public function uninstall(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."contacts`");
    }


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
        if (!isset($data['gdpr'])) {
            $data['gdpr'] = 0;
        }

        $this->db->query("INSERT INTO ".DB_PREFIX."contacts SET name = '".$data['name']."', `email` = '".$data['email']."', `gdpr` = '".(int) $data['gdpr']."',`message` = '".$data['message']."'");

        return $this->db->getLastId();
    }


    /**
     * Edit one contact
     *
     * @param  int  $id
     * @param  array  $data
     *
     * @return int
     */
    public function editContact(int $id, array $data): int
    {
        // gdpr checkbox
        if (!array_key_exists('gdpr', $data)) {
            $data['gdpr'] = 0;
        }

        $this->db->query("UPDATE ".DB_PREFIX."contacts SET name = '".$data['name']."', `email` = '".$data['email']."', `gdpr` = '".(int) $data['gdpr']."',`message` = '".$data['message']."' WHERE id = '".$id."'");

        return $this->db->getLastId();
    }


    /**
     * Delete one contact
     *
     * @param  int  $id
     *
     * @return void
     */
    public function deleteContact(int $id): void
    {
        $this->db->query("DELETE FROM ".DB_PREFIX."contacts WHERE id = '".(int) $id."'");
    }


    /**
     * Get one contact
     *
     * @param  int  $id
     *
     * @return mixed
     */
    public function getContact(int $id): mixed
    {
        $sql   = "SELECT * FROM ".DB_PREFIX."contacts where id='".$id."' ";
        $query = $this->db->query($sql);

        return $query->row;

    }


    /**
     * Get all contacts (paginated by params)
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function getContacts(array $data): mixed
    {
        $sql = "SELECT * FROM ".DB_PREFIX."contacts";

        $sort_data = [
            'submitted_date',
            'id',
            'email',
            'name',
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY ".$data['sort'];
        } else {
            $sql .= " ORDER BY submitted_date";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT ".(int) $data['start'].",".(int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;

    }


    /**
     * Get the number of contacts
     *
     * @return mixed
     */
    public function getTotalContacts(): mixed
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."contacts");

        return $query->row['total'];
    }


}

?>
