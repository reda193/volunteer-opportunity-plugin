<?php
class VolunteerView {
    public function display_table($volunteers) {
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
                        <?php if ($volunteers): ?>
                            <?php foreach($volunteers as $volunteer): ?>
                                <tr class="volunter-table-body-tr" id="row-<?php echo $volunteer['volunteer_id']; ?>">
                                    <td><?php echo esc_html($volunteer['volunteer_id']); ?></td>
                                    <td class="editable-cell" data-field="position">
                                        <?php echo esc_html($volunteer['position']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="organization">
                                        <?php echo esc_html($volunteer['organization']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="type">
                                        <?php echo esc_html($volunteer['type']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="email">
                                        <?php echo esc_html($volunteer['email']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="description">
                                        <?php echo esc_html($volunteer['description']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="location">
                                        <?php echo esc_html($volunteer['location']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="hours">
                                        <?php echo esc_html($volunteer['hours']); ?>
                                    </td>
                                    <td class="editable-cell" data-field="skills_required">
                                        <?php echo esc_html($volunteer['skills_required']); ?>
                                    </td>
                                    <td>
                                        <button class="edit-row button" 
                                                onclick="makeRowEditable(<?php echo $volunteer['volunteer_id']; ?>)">
                                            Edit
                                        </button>
                                        <button class="save-row button" 
                                                style="display:none;" 
                                                onclick="saveRow(<?php echo $volunteer['volunteer_id']; ?>)">
                                            Save
                                        </button>
                                        <button class="delete-row button" 
                                                onclick="deleteVolunteer(<?php echo $volunteer['volunteer_id']; ?>)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php wp_nonce_field('volunteer_delete_nonce', 'volunteer_delete_nonce'); ?>
            </div>
        </div>

        
        <?php
    }
}