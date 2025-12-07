<?php
require_once 'd:/webroot/BTL_LTW/BTL_LTW_BE/config/Database.php';

$database = new Database();
$db = $database->getConnection();

$tables = ['Lib_game', 'Orders', 'Order_items', 'Transaction', 'Payment'];

foreach ($tables as $table) {
    echo "Table: $table\n";
    try {
        $stmt = $db->query("DESCRIBE `$table`");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } catch (Exception $e) {
        echo "Table does not exist or error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
?>
