<?php
    /**
     * Plugin Name: Volunteer Plugin
     * Description: A plugin for listing volunteering opportunities
     * Author: Mohamed 
     * Version: 0.0.1
     * 
     */
     
     function volunter_admin_styles() {
            wp_enqueue_style(
                'volunteer-admin-style',
                plugins_url('style.css', __FILE__),
                array(),
            );
        }
    
    add_action('admin_enqueue_scripts', 'volunter_admin_styles');
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

     function myplugin_uninstall() {
        global $wpdb;
        $wpdb->query("DROP TABLE volunteer");
    }
    function get_volunteer() {
        global $wpdb;
        $query = $wpdb->get_results("SELECT * FROM volunteer");
        return $query;
    }


    function wp_volunteer_adminpage() {
        add_menu_page(
            "Volunteer Admin Page",
            "Volunteer",
            "manage_options",
            "volunteer",
            "wp_volunteer_adminpage_html",
            "icon_url",
            position: 1
        );
    }

    add_action('admin_menu', 'wp_volunteer_adminpage');
     register_activation_hook( __FILE__,"myplugin_activate");
     register_deactivation_hook( __FILE__,"myplugin_deactivate");
     register_uninstall_hook( __FILE__,"myplugin_uninstall");
?>