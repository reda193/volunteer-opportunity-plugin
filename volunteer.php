<?php
/**
 * Plugin Name: Volunteer Plugin
 * Description: A plugin for listing volunteering opportunities
 * Author: Mohamed Reda
 * Version: 0.0.1
 */

# Require Model/View/Controller files
require_once plugin_dir_path(__FILE__) . 'Model/VolunteerModel.php';
require_once plugin_dir_path(__FILE__) . 'View/VolunteerView.php';
require_once plugin_dir_path(__FILE__) . 'Controller/VolunteerController.php';

# Initialize the main controller for the plugin
$controller = new VolunteerController();

/**
 * Enqueues the admin stylesheet for the Volunteer plugin, and loads the file that styles the admin interface
 * @return void
 */
function volunterAdminStyles() {
        wp_enqueue_style(
            'volunteer-admin-style',
            plugins_url('style.css', __FILE__),
            array(),
        );
    }

add_action('admin_enqueue_scripts', 'volunterAdminStyles');


 

 /**
  * Adds menu page for managing volunteer opportunities in the admin page.
  * @return void
  */
 function add_volunteer_menu() {
    add_menu_page(
        'Volunteer Opportunities', 
        'Volunteers',             
        'manage_options',         
        'volunteer',        
        'volunteer_page_router', 
        5
    );
}


 /**
  * Allows wrapper for the displayAdminPage method in the controller
  * @return void
  */
 function displayVolunterPage() {
    global $controller;
    $controller->displayAdminPage();
}

/**
 * Router that handles different views like the create form, and the main admin table.
 * 
 * @return void
 */
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

/**
 * Enqueues the admin stylesheet for the shortcode
 * @return void
 */
function volunteer_enqueue_styles() {
    wp_enqueue_style(
        'volunteer-shortcode-styles',
        plugins_url('volunteer-styles.css', __FILE__),
        array(),
    );
}
add_action('wp_enqueue_scripts', 'volunteer_enqueue_styles');

/**
 * Handles the [volunteer] shortcode, it calls the handleShortcode method, and sends attributes in the parameters.
 * @param mixed $atts
 * @return bool|string
 */
function volunteer_shortcode($atts) {
    global $controller;
    return $controller->handleShortcode($atts);
}

add_shortcode('volunteer', 'volunteer_shortcode');

add_action('admin_menu', 'add_volunteer_menu');

# Activates table
register_activation_hook(__FILE__, function() {
    $model = new VolunterModel();
});

# Deletes table
register_deactivation_hook(__FILE__, function() {
    $model = new VolunterModel();
    $model->deleteTable();
});

?>