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
        'volunteer',        
        'volunteer_page_router', 
        'dashicons-groups',    
        1  
    );
}


 function displayVolunterPage() {
    global $controller;
    $controller->displayAdminPage();
}

function volunteer_page_router() {
    global $controller;
    $page = isset($_GET['page']) ? $_GET['page'] : '';
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($page === 'volunteer' && $action === 'create') {
        $controller->displayCreateForm(); 
    } else {
        $controller->displayAdminPage();
    }
}

function volunteer_enqueue_styles() {
    wp_enqueue_style(
        'volunteer-shortcode-styles',
        plugins_url('volunteer-styles.css', __FILE__),
        array(),
    );
}
add_action('wp_enqueue_scripts', 'volunteer_enqueue_styles');

function volunteer_shortcode($atts) {
    global $controller;
    return $controller->handleShortcode($atts);
}
add_shortcode('volunteer', 'volunteer_shortcode');

add_action('admin_menu', 'add_volunteer_menu');

register_activation_hook(__FILE__, function() {
    $model = new VolunterModel();
});

register_deactivation_hook(__FILE__, function() {
    $model = new VolunterModel();
    $model->deleteTable();
});

?>