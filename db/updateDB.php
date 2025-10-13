<?php
/* updateDB.php */

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

$numToStatus = [ // map status num to word
    1 => 'draft',
    2 => 'paid',
    3 => 'pending',
];

$statusToNum = [
    'draft' => 1,
    'paid' => 2,
    'pending' => 3,
];


function addInvoice($db, $invoiceData, $statusToNum) {
    $invoiceStatus = $statusToNum[strtolower($invoiceData['status'])] ?? 1;
    $sql = "INSERT INTO invoices (number, amount, status_id, client, email) 
    VALUES (:number, :amount, :status_id, :client, :email)";
    $setInv = $db->prepare($sql);
    $setInv->bindParam(':number', $invoiceData['number']);
    $setInv->bindParam(':amount', $invoiceData['amount']);
    $setInv->bindParam(':status_id', $invoiceStatus, PDO::PARAM_INT);
    $setInv->bindParam(':client', $invoiceData['client']);
    $setInv->bindParam(':email', $invoiceData['email']);

    return $setInv->execute();
}

function deleteInvoice($db, $invoiceNumber) {
    $sql = "DELETE FROM invoices WHERE number = :number";
    $delInv = $db->prepare($sql);

    $delInv->bindParam(':number', $invoiceNumber);
    return $delInv->execute();
}

foreach ($invoices as &$invoice) {
    $invoice['status'] = $statuses[$invoice['status_id']] ?? 'unknown';
}
unset($invoice); // destroy invoice var after iteration

?>