<?php
require_once 'd:/webroot/BTL_LTW/BTL_LTW_BE/config/Database.php';

$database = new Database();
$db = $database->connect();

echo "Table: Cart\n";
$stmt = $db->query("DESCRIBE `Cart`");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}

echo "\nTable: Cart_game\n";
$stmt = $db->query("DESCRIBE `Cart_game`");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>
