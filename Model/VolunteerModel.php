<?php
class VolunterModel {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS volunteer (
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

    public function delete_table() {
        $this->wpdb->query("DROP TABLE volunteer");
    }
}