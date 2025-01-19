<?php
class VolunteerController {
    private $wpdb;
    private $view;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->model = new VolunterModel();
        $this->view = new VolunteerView();

        add_action('wp_ajax_update_volunteer', array($this, 'handleUpdateAjax'));
        add_action('wp_ajax_delete_volunteer', array($this, 'handleDeleteAjax'));
        add_action('wp_ajax_create_volunteer', array($this, 'handleCreateAjax'));

    }
    public function displayCreateForm() {
        $this->view->displayCreateForm();
    }
    public function displayAdminPage() {
        $volunteers = $this->getAllVolunteers();
        $this->view->displayAdminTable($volunteers);
    }
    public function handleUpdateAjax() {
        check_ajax_referer('volunteerUpdateNonce', 'nonce');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$id) {
            wp_send_json_error('Invalid ID');
            return;
        }

        $data = array(
            'position' => isset($_POST['position']) ? sanitize_text_field($_POST['position']) : '',
            'organization' => isset($_POST['organization']) ? sanitize_text_field($_POST['organization']) : '',
            'type' => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '',
            'email' => isset($_POST['email']) ? sanitize_email($_POST['email']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'location' => isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '',
            'hours' => isset($_POST['hours']) ? intval($_POST['hours']) : 0,
            'skills_required' => isset($_POST['skills_required']) ? sanitize_textarea_field($_POST['skills_required']) : ''
        );

        $result = $this->updateVolunteer($id, $data);

        if ($result !== false) {
            wp_send_json_success('Updated successfully');
        } else {
            wp_send_json_error('Update failed: ' . $this->wpdb->last_error);
        }
    }

    public function handleCreateAjax() {
        check_ajax_referer('volunteerCreateNonce', 'nonce');
    
        $data = array(
            'position' => isset($_POST['position']) ? sanitize_text_field($_POST['position']) : '',
            'organization' => isset($_POST['organization']) ? sanitize_text_field($_POST['organization']) : '',
            'type' => isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '',
            'email' => isset($_POST['email']) ? sanitize_email($_POST['email']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'location' => isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '',
            'hours' => isset($_POST['hours']) ? intval($_POST['hours']) : 0,
            'skills_required' => isset($_POST['skills_required']) ? sanitize_textarea_field($_POST['skills_required']) : ''
        );
    
        $result = $this->createVolunteer($data);
    
        if ($result !== false) {
            wp_send_json_success('Volunteer created successfully');
        } else {
            wp_send_json_error('Create failed: ' . $this->wpdb->last_error);
        }
    }
    

    public function handleDeleteAjax() {
        check_ajax_referer('volunteerUpdateNonce', 'nonce');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$id) {
            wp_send_json_error('Invalid ID');
            return;
        }

        $result = $this->deleteVolunteer($id);

        if ($result !== false) {
            wp_send_json_success('Deleted successfully');
        } else {
            wp_send_json_error('Delete failed: ' . $this->wpdb->last_error);
        }
    }

    public function getAllVolunteers() {
        return $this->wpdb->get_results("SELECT * FROM volunteer", ARRAY_A);
    }
    public function getVolunteerById($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM volunteer WHERE volunteer_id = %d", $id),
            ARRAY_A
        );
    }

    public function handleShortcode($atts) {
        $atts = shortcode_atts(array(
            'hours' => null,
            'type' => null
        ), $atts, 'volunteer');

        $volunteers = $this->getVolunteersWithFilters($atts);
        return $this->view->displayShortcodeContent($volunteers, $atts);
    }

    private function getVolunteersWithFilters($atts) {
        global $wpdb;
        $table_name = 'volunteer';
        
        $sql = "SELECT * FROM $table_name";
        $where_clauses = array();
        $values = array();

        if (isset($atts['hours']) && !empty($atts['hours'])) {
            $where_clauses[] = "hours < %d";
            $values[] = intval($atts['hours']);
        }

        if (isset($atts['type']) && !empty($atts['type'])) {
            $where_clauses[] = "type = %s";
            $values[] = $atts['type'];
        }

        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
            $sql = $wpdb->prepare($sql, $values);
        }

        return $wpdb->get_results($sql, ARRAY_A);
    }
    public function createVolunteer($data) {
        return $this->wpdb->insert(
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
        );
    }

    public function updateVolunteer($id, $data) {
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

    public function deleteVolunteer($id) {
        return $this->wpdb->delete(
            'volunteer',
            array('volunteer_id' => $id),
            array('%d')
        );
    }
}