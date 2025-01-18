<?php
/**
 * Plugin Name: Volunteer Plugin
 * Description: A plugin for listing volunteering opportunities
 * Author: Mohamed 
 * Version: 0.0.1
 */

 require_once plugin_dir_path(__FILE__) . 'Model/VolunteerModel.php';
 require_once plugin_dir_path(__FILE__) . 'View/VolunteerView.php';
 require_once plugin_dir_path(__FILE__) . 'Controller/VolunteerController.php';


function volunterAdminStyles() {
        wp_enqueue_style(
            'volunteer-admin-style',
            plugins_url('style.css', __FILE__),
            array(),
        );
    }

add_action('admin_enqueue_scripts', 'volunterAdminStyles');

 $controller = new VolunteerController();

 function add_volunteer_menu() {
    add_menu_page(
        'Volunteer Opportunities', 
        'Volunteers',             
        'manage_options',         
        'volunteer-list',        
        'displayVolunterPage', 
        'dashicons-groups',      
    );
}


 function displayVolunterPage() {
    global $controller;
    $controller->displayAdminPage();
}

add_action('admin_menu', 'add_volunteer_menu');

register_activation_hook(__FILE__, function() {
    $model = new VolunterModel();
});

register_deactivation_hook(__FILE__, function() {
    $model = new VolunterModel();
    $model->deleteTable();
});

?>