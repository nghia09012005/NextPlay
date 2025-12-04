<?php
require_once 'config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = file_get_contents('seed_data.sql');
    
    // Split by semicolon to execute multiple queries
    // This is a simple split, might break if semicolons are inside strings, but for this seed file it's fine
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $db->exec($query);
                echo "Executed: " . substr($query, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage() . "\n";
                echo "Query: " . $query . "\n";
            }
        }
    }
    
    echo "Database seed completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
