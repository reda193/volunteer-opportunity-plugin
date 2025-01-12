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

     function myplugin_uninstall() {
        global $wpdb;
        $wpdb->query("DROP TABLE volunteer");
    }
    function get_volunteer() {
        global $wpdb;
        $query = $wpdb->get_results("SELECT * FROM volunteer");
        return $query;
    }
    function wp_volunteer_adminpage_html() {

        $volunteers = get_volunteer();
        
        ?>
        <div class="volunteer-body">
            <h1 class="volunteer-header"><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="create-button">
                <button>Create</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Position</th>
                        <th>Organization</th>
                        <th>Type</th>
                        <th>E-mail</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Hours</th>
                        <th>Skills Required</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ( $volunteers ) {
                            foreach($volunteers as $volunteer) {
                                ?>
                                    <tr>
                                        <td><?php echo esc_html($volunteer->volunteer_id); ?></td>
                                        <td><?php echo esc_html($volunteer->position); ?></td>
                                        <td><?php echo esc_html($volunteer->organization); ?></td>
                                        <td><?php echo esc_html($volunteer->type); ?></td>
                                        <td><?php echo esc_html($volunteer->email); ?></td>
                                        <td><?php echo esc_html($volunteer->description); ?></td>
                                        <td><?php echo esc_html($volunteer->location); ?></td>
                                        <td><?php echo esc_html($volunteer->hours); ?></td>
                                        <td><?php echo esc_html($volunteer->skills_required); ?></td>
                                        <td>
                                            <a href="#">Edit</a>
                                            <a href="#">Delete</a>
                                        </td>
                                    </tr>
                                <?php
                            }
                        }
    
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    function wp_volunteer_adminpage() {
        add_menu_page(
            "Volunteer",
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