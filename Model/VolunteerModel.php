<?php
/**
 * Model class for managing volunteer data in the database.
 * 
 * This class handles all database operations including table creation,
 * deletion, and data management for volunteer opportunities. It uses
 * WordPress's wpdb class for database interactions.
 * 
 * 
 */
class VolunteerModel {
    /**
     * WordPress database instance
     * @var wpdb
     */
    private $wpdb;

    /**
     * Initializes the model and creates the volunteer table if it doesn't exist.
     * 
     * Creates a table with the following structure:
     * - volunteer_id: Auto-incrementing primary key
     * - position: Title/name of the volunteer position
     * - organization: Name of the organization offering the position
     * - type: Type of volunteer work (One-Time, Recurring, or Seasonal)
     * - email: Contact email for the position
     * - description: Detailed description of the volunteer work
     * - location: Where the volunteer work takes place
     * - hours: Number of hours required
     * - skills_required: List of required skills or qualifications
     * 
     * 
     */
    public function __construct() {
        // Get WordPress database instance
        global $wpdb;
        $this->wpdb = $wpdb;

        // Create table with specified schema
        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS volunteer (
                volunteer_id int AUTO_INCREMENT PRIMARY KEY,
                position varchar(255) NOT NULL,
                organization varchar(255) NOT NULL,
                type ENUM('One-Time', 'Recurring', 'Seasonal') NOT NULL,
                email varchar(255) NOT NULL,
                description varchar(255) NOT NULL,
                location varchar(255),
                hours int NOT NULL,
                skills_required TEXT
            );"
        );
    }

    /**
     * Deletes the volunteer table from the database.
     * 
     * This method is typically called during plugin deactivation
     * to clean up the database if needed. Use with caution as it
     * will permanently delete all volunteer data.
     * 
     * 
     * @return void
     */
    public function deleteTable() {
        $this->wpdb->query("DROP TABLE volunteer");
    }


}