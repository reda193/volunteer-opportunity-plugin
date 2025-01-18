<?php
/**
 * Plugin Name: Volunteer Plugin
 * Description: A plugin for listing volunteering opportunities
 * Author: Mohamed 
 * Version: 0.0.1
 */

require_once plugin_dir_path(__FILE__) . 'Model/VolunteerModel.php';


register_activation_hook(__FILE__, function() {
    $model = new VolunterModel();
});

register_deactivation_hook(__FILE__, function() {
    $model = new VolunterModel();
    $model->delete_table();
});

?>