<?php
class VolunteerView {
    public function displayAdminTable($volunteers) {
        wp_nonce_field('volunteerUpdateNonce', 'volunteerNonce');
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
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                window.makeRowEditable = function(id) {
                    const row = document.getElementById('row-' + id);
                    const editableCells = row.getElementsByClassName('editable-cell');
                    
                    for (let cell of editableCells) {
                        const currentValue = cell.textContent.trim();
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
                    
                    row.querySelector('.edit-row').style.display = 'none';
                    row.querySelector('.save-row').style.display = 'inline-block';
                };

                window.saveRow = function(id) {
                    const row = document.getElementById('row-' + id);
                    const nonce = document.getElementById('volunteerNonce').value;
                    
                    const data = {
                        action: 'update_volunteer',
                        nonce: nonce,
                        id: id
                    };
                    
                    row.querySelectorAll('.editable-cell').forEach(cell => {
                        const field = cell.getAttribute('data-field');
                        const input = cell.querySelector('input, select, textarea');
                        data[field] = input.value;
                    });

                    console.log('Sending data:', data);
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            console.log('Response:', response);
                            if (response.success) {
                                row.querySelectorAll('.editable-cell').forEach(cell => {
                                    const input = cell.querySelector('input, select, textarea');
                                    cell.textContent = input.value;
                                });
                                
                                row.querySelector('.edit-row').style.display = 'inline-block';
                                row.querySelector('.save-row').style.display = 'none';
                                
                                alert('Changes saved successfully!');
                            } else {
                                alert('Error saving changes: ' + (response.data || 'Unknown error'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            alert('Error saving changes. Check console for details.');
                        }
                    });
                };

                window.deleteVolunteer = function(id) {
                    if (!confirm('Are you sure you want to delete this volunteer record?')) {
                        return;
                    }
                    const nonce = document.getElementById('volunteerNonce').value;
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'deleteVolunteer',
                            nonce: nonce,
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                const row = document.getElementById('row-' + id);
                                row.remove();
                                alert('Record deleted successfully!');
                            } else {
                                alert('Error deleting record: ' + (response.data || 'Unknown error'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            alert('Error deleting record. Check console for details.');
                        }
                    });
                };
            });
        </script>
        <?php
    }
}
