<?php
/* fetchDB.php */

try {
    $dsn = 'sqlite:invoice_manager.sqlite';
    $db = new PDO($dsn);

} catch (PDOException $e) {
    print $e->getMessage() . "<br/>";
    exit();
}

$sql = 'SELECT * FROM invoices';

$invQuery = $db->query("SELECT * FROM invoices");
$invoices = $invQuery->fetchAll(PDO::FETCH_ASSOC);

$statuses = [ // map status num to word
    1 => 'draft',
    2 => 'paid',
    3 => 'pending',
];

foreach ($invoices as &$invoice) {
    $invoice['status'] = $statuses[$invoice['status_id']] ?? 'unknown';
}
unset($invoice); // destroy invoice var after iteration

?>