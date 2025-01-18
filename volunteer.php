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

    function wp_volunteer_adminpage_html() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        switch ($action) {
            case 'create':
                wp_volunteer_create_page();
                break;
            default:
                wp_volunteer_get_page();
                break;
        }
        
    
    }

    function wp_volunteer_get_page() {
        $volunteers = get_volunteer();
        ?>
        <div class="volunteer-body">
            <h1 class="volunteer-header"><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="create-button">
                <a href="<?php echo admin_url('admin.php?page=volunteer&action=create'); ?>" class="button">Create</a>
            </div>
            <div class="table-container">
                <table class="volunteer-table">
                    <thead class="volunteer-table-header">
                        <tr class="volunteer-table-header-tr">
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
                    <tbody class="volunter-table-body">
                        <?php
                        if ($volunteers) {
                            foreach($volunteers as $volunteer) {
                                ?>
                                <tr class="volunter-table-body-tr" id="row-<?php echo $volunteer->volunteer_id; ?>">
                                    <td><input type="checkbox" id="volunteer-<?php echo $volunteer->volunteer_id; ?>" name="volunteer[]" value="<?php echo $volunteer->volunteer_id; ?>">
                                        <?php echo esc_html($volunteer->volunteer_id); ?>
                                    </td>
                                    <td class="editable-cell" data-field="position"><?php echo esc_html($volunteer->position); ?></td>
                                    <td class="editable-cell" data-field="organization"><?php echo esc_html($volunteer->organization); ?></td>
                                    <td class="editable-cell" data-field="type"><?php echo esc_html($volunteer->type); ?></td>
                                    <td class="editable-cell" data-field="email"><?php echo esc_html($volunteer->email); ?></td>
                                    <td class="editable-cell" data-field="description"><?php echo esc_html($volunteer->description); ?></td>
                                    <td class="editable-cell" data-field="location"><?php echo esc_html($volunteer->location); ?></td>
                                    <td class="editable-cell" data-field="hours"><?php echo esc_html($volunteer->hours); ?></td>
                                    <td class="editable-cell" data-field="skills_required"><?php echo esc_html($volunteer->skills_required); ?></td>
                                    <td>
                                        <button class="edit-row button" onclick="makeRowEditable(<?php echo $volunteer->volunteer_id; ?>)">Edit</button>
                                        <button class="save-row button" style="display:none;" onclick="saveRow(<?php echo $volunteer->volunteer_id; ?>)">Save</button>
                                        <a href="#" class="button">Delete</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <script>
            function makeRowEditable(id) {
                const row = document.getElementById('row-' + id);
                const editableCells = row.getElementsByClassName('editable-cell');
                
                // Convert each cell to an input field
                for (let cell of editableCells) {
                    const currentValue = cell.textContent;
                    const field = cell.getAttribute('data-field');
                    
                    if (field === 'type') {
                        cell.innerHTML = `
                            <select>
                                <option value="one-time" ${currentValue === 'one-time' ? 'selected' : ''}>One-time</option>
                                <option value="recurring" ${currentValue === 'recurring' ? 'selected' : ''}>Recurring</option>
                                <option value="seasonal" ${currentValue === 'seasonal' ? 'selected' : ''}>Seasonal</option>
                            </select>
                        `;
                    } else if (field === 'description' || field === 'skills_required') {
                        cell.innerHTML = `<textarea>${currentValue}</textarea>`;
                    } else {
                        cell.innerHTML = `<input type="text" value="${currentValue}">`;
                    }
                }
                
                // Toggle buttons
                row.querySelector('.edit-row').style.display = 'none';
                row.querySelector('.save-row').style.display = 'inline-block';
            }
            </script>
        </div>
        <?php
    }
    function wp_volunteer_create_page() {
        ?>
            <H1>TEST</H1>
        <?php
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