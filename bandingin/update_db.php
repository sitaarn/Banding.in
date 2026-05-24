<?php
if (file_exists(__DIR__ . '/config/config.php')) {
    require_once __DIR__ . '/config/config.php';
}

try {
    $db = getDB();
    
    // Add product_name_snapshot column if it doesn't exist
    try {
        $db->exec("ALTER TABLE product_reports ADD COLUMN product_name_snapshot VARCHAR(255) NULL AFTER product_id");
        echo "Column product_name_snapshot added.\n";
    } catch(PDOException $e) {
        echo "Column might already exist or error: " . $e->getMessage() . "\n";
    }
    
    // Drop foreign key if it exists so we can keep reports when product is deleted
    try {
        $db->exec("ALTER TABLE product_reports DROP FOREIGN KEY product_reports_ibfk_1");
        echo "Foreign key product_reports_ibfk_1 dropped.\n";
    } catch(PDOException $e) {
        echo "Foreign key might not exist or error: " . $e->getMessage() . "\n";
    }

    echo "Database update completed successfully.\n";
} catch(Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
}
