<?php
/**
 * View class for rendering volunteer opportunities
 * 
 * Handles HTML output and JavaScript functionality, shortcode, table, and forms.
 */
class VolunteerView {
    /**
     * Displays the admin table with all volunteer opprtunities
     * Inclides functionaltiy for inline editing and deleting
     * @param mixed $volunteers
     * @return void
     */
    public function displayAdminTable($volunteers) {
        # Generates nonce
        wp_nonce_field('volunteerUpdateNonce', 'volunteerNonce');
        ?>
        <div class="volunteer-body">
            <h1 class="volunteer-header"><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="create-button">
                <a href="<?php echo admin_url('admin.php?page=volunteer&action=create'); ?>" class="button">Create</a>
            </div>
            <!-- Table Container -->
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
                    <!-- Table Body Container -->
                    <tbody class="volunter-table-body">
                        <?php if ($volunteers): ?>
                            <?php 
                            # Creates display ID country
                            $displayId = 1; 
                            foreach($volunteers as $volunteer): 
                            ?>  
                                <!-- Table rows iwth data attributes for JavaScript functionality -->
                                <tr class="volunteer-table-body-tr"
                                    id="row-<?php echo $displayId; ?>" 
                                    data-original-id="<?php echo $volunteer['volunteer_id']; ?>">
                                    <td><?php echo $displayId; ?></td> 
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
                                    <!-- Action Buttons -->
                                    <td>
                                        <button class="edit-row button" 
                                                onclick="makeRowEditable(<?php echo $displayId; ?>)">
                                            Edit
                                        </button>
                                        <button class="save-row button" 
                                                style="display:none;" 
                                                onclick="saveRow(<?php echo $displayId; ?>)">
                                                Save
                                        </button>
                                        <button class="delete-row button" 
                                                onclick="deleteVolunteer(<?php echo $volunteer['volunteer_id']; ?>)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                            $displayId++;
                            endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
           jQuery(document).ready(function($) {
    // Declare functions in global scope
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

    window.saveRow = function(displayId) {
        const row = document.getElementById('row-' + displayId);
        if (!row) {
            console.error('Row not found:', displayId);
            return;
        }

        const originalId = row.getAttribute('data-original-id');
        if (!originalId) {
            console.error('Original ID not found for row:', displayId);
            return;
        }

        const nonce = document.getElementById('volunteerNonce').value;
        
        // Prepare data for request
        const data = {
            action: 'update_volunteer',
            nonce: nonce,
            id: originalId // Use the original database ID instead of display ID
        };

        // Collect values from editable cells
        let hasErrors = false;
        row.querySelectorAll('.editable-cell').forEach(cell => {
            const field = cell.getAttribute('data-field');
            const input = cell.querySelector('input, select, textarea');
            
            if (!input) {
                console.error(`Input element not found for field: ${field}`);
                hasErrors = true;
                return;
            }

            // Basic validation
            if (input.required && !input.value.trim()) {
                alert(`${field} cannot be empty`);
                hasErrors = true;
                return;
            }

            if (field === 'email' && !isValidEmail(input.value)) {
                alert('Please enter a valid email address');
                hasErrors = true;
                return;
            }

            if (field === 'hours' && (isNaN(input.value) || input.value < 0)) {
                alert('Hours must be a positive number');
                hasErrors = true;
                return;
            }

            data[field] = input.value;
        });

        if (hasErrors) {
            return;
        }

        // Show loading state
        const saveButton = row.querySelector('.save-row');
        const originalText = saveButton.textContent;
        saveButton.textContent = 'Saving...';
        saveButton.disabled = true;

        // Send request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    // Update the displayed values
                    row.querySelectorAll('.editable-cell').forEach(cell => {
                        const input = cell.querySelector('input, select, textarea');
                        cell.textContent = input.value;
                    });
                    
                    // Reset buttons
                    row.querySelector('.edit-row').style.display = 'inline-block';
                    row.querySelector('.save-row').style.display = 'none';
                    
                    alert('Changes saved successfully!');
                } else {
                    alert('Error saving changes: ' + (response.data || 'Unknown error'));
                    console.error('Save error:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Error saving changes. Please try again.');
            },
            complete: function() {
                // Reset button state
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            }
        });
    };

    window.deleteVolunteer = function(id) {
        if (!confirm('Are you sure you want to delete this volunteer record?')) {
            return;
        }
        const nonce = document.getElementById('volunteerNonce').value;
        
        // Send delete request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_volunteer',
                nonce: nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    const row = $(`tr[data-original-id="${id}"]`);
                    
                    if (row.length) {
                        row.remove(); 
                        
                        // Update remaining row IDs and buttons
                        $('.volunteer-table-body-tr').each(function(index) {
                            const newIndex = index + 1;
                            $(this).find('td:first-child').text(newIndex);
                            $(this).attr('id', 'row-' + newIndex);
                            $(this).find('.edit-row').attr('onclick', `makeRowEditable(${newIndex})`);
                            $(this).find('.save-row').attr('onclick', `saveRow(${newIndex})`);
                        });
                        
                        alert('Record deleted successfully!');
                    } else {
                        console.error('Row not found for id: ' + id);
                        alert('Record deleted, but unable to remove from table.');
                    }
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

    // Helper function to validate email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Initialize any other event handlers
    $('#volunteer-create-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'create_volunteer',
            nonce: $('#volunteerNonce').val(),
        };
        
        $(this).find('input, select, textarea').each(function() {
            formData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Volunteer created successfully!');
                    window.location.href = '<?php echo admin_url('admin.php?page=volunteer'); ?>';
                } else {
                    alert('Error creating volunteer: ' + (response.data || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Error creating volunteer. Check console for details.');
            }
        });
    });
});
        </script>
        <?php
    }

    /**
     * Displays the creare form
     * @return void
     */
    public function displayCreateForm() {
        // Generates nonce
        wp_nonce_field('volunteerCreateNonce', 'volunteerNonce');
        ?>
        <div class="volunteer-form">
            <h1 class="volunteer-header">Create Volunteer</h1>
            <form id="volunteer-create-form">
                <div class="form-group">
                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position" required>
                </div>
                <div class="form-group">
                    <label for="organization">Organization:</label>
                    <input type="text" id="organization" name="organization" required>
                </div>
                <div class="form-group">
                    <label for="type">Type:</label>
                    <select id="type" name="type" required>
                        <option value="one-time">One-time</option>
                        <option value="recurring">Recurring</option>
                        <option value="seasonal">Seasonal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="hours">Hours:</label>
                    <input type="number" id="hours" name="hours" required>
                </div>
                <div class="form-group">
                    <label for="skills_required">Skills Required:</label>
                    <textarea id="skills_required" name="skills_required"></textarea>
                </div>
                <button type="submit" class="button">Save Volunteer</button>
            </form>
        </div>

        <script>
            jQuery(document).ready(function($) {
                /**
                 * Handles submission through AJAX
                 */
                $('#volunteer-create-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    // Prepares FORM DATA
                    const formData = {
                        action: 'create_volunteer',
                        nonce: $('#volunteerNonce').val(),
                    };
                    
                    // Collects form field values
                    $(this).find('input, select, textarea').each(function() {
                        formData[$(this).attr('name')] = $(this).val();
                    });

                    // Sends AJAX request
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                alert('Volunteer created successfully!');
                                window.location.href = '<?php echo admin_url('admin.php?page=volunteer'); ?>';
                            } else {
                                alert('Error creating volunteer: ' + (response.data || 'Unknown error'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            alert('Error creating volunteer. Check console for details.');
                        }
                    });
                });
            });
        </script>
        <?php
    }

    /**
     * Dispalys the shortcode content 
     * @param mixed $volunteers
     * @param mixed $atts
     * @return bool|string
     */
    public function displayShortcodeContent($volunteers, $atts) {
        ob_start();
        ?>
        <table class="volunteer-table">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Organization</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Hours</th>
                    <th>Skills Required</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($volunteers) {
                    foreach ($volunteers as $volunteer) {
                        $hours = intval($volunteer['hours']);
                        $row_class = $this->getRowClass($hours, $atts);
                        ?>
                        <tr class="<?php echo esc_attr($row_class); ?>">
                            <td class="volunteer-position"><?php echo esc_html($volunteer['position']); ?></td>
                            <td class="volunteer-organization"><?php echo esc_html($volunteer['organization']); ?></td>
                            <td class="volunteer-type"><?php echo esc_html($volunteer['type']); ?></td>
                            <td><a href="mailto:<?php echo esc_attr($volunteer['email']); ?>"><?php echo esc_html($volunteer['email']); ?></a></td>
                            <td><?php echo esc_html($volunteer['description']); ?></td>
                            <td><?php echo esc_html($volunteer['location']); ?></td>
                            <td class="volunteer-hours"><?php echo esc_html($volunteer['hours']); ?></td>
                            <td class="volunteer-skills"><?php echo esc_html($volunteer['skills_required']); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

    /**
     * Determines CSS based on hours
     * @param mixed $hours
     * @param mixed $atts
     * @return string
     */
    private function getRowClass($hours, $atts) {
        if (empty($atts['hours']) && empty($atts['type'])) {
            if ($hours < 10) {
                return 'hours-low';
            } elseif ($hours <= 100) {
                return 'hours-medium';
            } else {
                return 'hours-high';
            }
        }
        return '';
    }

}
