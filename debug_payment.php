<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/model/User.php';
require_once __DIR__ . '/model/Customer.php';
require_once __DIR__ . '/model/Game.php';

// 1. Connect
$database = new Database();
$db = $database->getConnection();

echo "--- Debugging Payment Logic ---\n";

// 2. Find User
$query = "SELECT * FROM User WHERE uname = 'minhtien2005'";
$stmt = $db->prepare($query);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User 'minhtien2005' not found.\n");
}
echo "User Found: ID=" . $user['uid'] . ", Username=" . $user['uname'] . "\n";

// 3. Find Customer Balance
$customerModel = new Customer($db);
$customer = $customerModel->readOne($user['uid']);
if (!$customer) {
    echo "Customer record not found for this user.\n";
} else {
    echo "Customer Balance: " . $customer['balance'] . "\n";
}

// 4. Find Game
$query = "SELECT * FROM Game WHERE name LIKE '%Grand Theft Auto V%'";
$stmt = $db->prepare($query);
$stmt->execute();
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game 'Grand Theft Auto V' not found.\n");
}
echo "Game Found: ID=" . $game['Gid'] . ", Name=" . $game['name'] . ", Price=" . $game['price'] . "\n";

// 5. Test SUM Query logic
$gameIds = [$game['Gid']];
$placeholders = str_repeat('?,', count($gameIds) - 1) . '?';
$query = "SELECT SUM(price) as total FROM `Game` WHERE Gid IN ($placeholders)";
$stmt = $db->prepare($query);
$stmt->execute($gameIds);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalCost = (float)($result['total'] ?? 0);

echo "Calculated Total Cost (via SQL SUM): " . $totalCost . "\n";

// 6. Logic Check
if ($customer) {
    $balance = (float)$customer['balance'];
    if ($balance < $totalCost) {
        echo "Logic Check: INSUFFICIENT BALANCE ($balance < $totalCost). Should fail.\n";
    } else {
        echo "Logic Check: SUFFICIENT BALANCE ($balance >= $totalCost). Should pass.\n";
    }
}
?>
