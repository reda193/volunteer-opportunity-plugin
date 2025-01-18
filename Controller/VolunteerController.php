<?php

class VolunteerController {
    private $wpdb;
    private $view;  // Add view property

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->view = new VolunteerView();  // Initialize view in constructor
    }

    public function displayAdminPage() {
        $volunteers = $this->getAllVolunteers();
        $this->view->display_table($volunteers);
    }

    // Get all volunteers
    public function getAllVolunteers() {
        return $this->wpdb->get_results("SELECT * FROM volunteer", ARRAY_A);
    }

    public function getVolunteerById($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM volunteer WHERE volunteer_id = %d", $id),
            ARRAY_A
        );
    }
    public function createVolunteer($data) {
        return $this->wpdb->insert( 
            $this->wpdb->insert(
                'volunteer',
                array(
                    'position' => $data['position'],
                    'organization' => $data['organization'],
                    'type' => $data['type'],
                    'email' => $data['email'],
                    'description' => $data['description'],
                    'location' => $data['location'],
                    'hours' => $data['hours'],
                    'skills_required' => $data['skills_required']
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')

            )
        );
    }

    public function update_volunteer($id, $data) {
        return $this->wpdb->update(
            'volunteer',
            array(
                'position' => $data['position'],
                'organization' => $data['organization'],
                'type' => $data['type'],
                'email' => $data['email'],
                'description' => $data['description'],
                'location' => $data['location'],
                'hours' => $data['hours'],
                'skills_required' => $data['skills_required']
            ),
            array('volunteer_id' => $id),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'),
            array('%d')
        );
    }

    public function delete_volunteer($id) {
        return $this->wpdb->delete(
            'volunteer',
            array('volunteer_id' => $id),
            array('%d')
        );
    }
}
?>