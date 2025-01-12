<?php
    /**
     * Plugin Name: Volunteer Plugin
     * Description: A plugin for listing volunteering opportunities
     * Author: Mohamed 
     * Version: 0.0.1
     * 
     */

     function myplugin_activate() {
        global $wpdb;
        $wpdb->query("CREATE TABLE volunteer (
                            volunteer_id int AUTO_INCREMENT PRIMARY KEY,
                            position varchar(255) NOT NULL,
                            organization varchar(255) NOT NULL,
                            type ENUM('one-time', 'recurring', 'seasonal') NOT NULL,
                            email varchar(255) NOT NULL,
                            description varchar(255) NOT NULL,
                            location varchar(255),
                            hours int NOT NULL,
                            skills_required TEXT
                            );");

     }

     function myplugin_deactivate() {
        global $wpdb;
        $wpdb->query("DROP TABLE volunteer");
     }

     register_activation_hook( __FILE__,"myplugin_activate");
     register_deactivation_hook( __FILE__,"myplugin_deactivate");
?>