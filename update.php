<?php
/* update.php */

session_start();

/* functions */

function deleteInvoiceFromValue($invoices, $key, $val) {
    foreach ($invoices as $subKey => $subArray) {
        if ($subArray[$key] == $val) {
            unset($invoices[$subKey]);
        }
    }
    return $invoices;
} // https://stackoverflow.com/a/4466437
function findByID($invoices) {
    global $currUpdateInvoiceID;
    return ($invoices['number'] == $currUpdateInvoiceID);
}

$currUpdateInvoiceStatus = NULL;

/* if invoiceToUpdate is passed, pull that invoice's data from $_SESSION['sessionInvoice'] into local variables */
if (isset($_GET['invoiceToUpdate'])) {
    $currUpdateInvoiceID = $_GET['invoiceToUpdate'];
    foreach (array_filter($_SESSION['sessionInvoice'], 'findByID') as $entry) {
        $currUpdateInvoiceAmount = $entry['amount'];
        $currUpdateInvoiceStatus = $entry['status'];
        $currUpdateInvoiceClient = $entry['client'];
        $currUpdateInvoiceEmail = $entry['email'];
    }
}
/* if invoiceToDelete is set, pass to deleteInvoiceFromValue func with invoice number and redirect to index */
elseif (isset($_GET['invoiceToDelete'])) {
    $_SESSION['sessionInvoice'] = deleteInvoiceFromValue($_SESSION['sessionInvoice'], 'number', $_GET['invoiceToDelete']);
    header('Location: /index.php', true, 302);
    exit;
}
/* if $_SESSION['retryInvoiceNumber'} is set, set ..Number, ..Amount,..Status,..Client and ..Email to
their respective local $currUpdateInvoice* variables to not require an if/elseif statement when displaying in html */
elseif (isset($_SESSION['retryInvoiceNumber'])) {
    $currUpdateInvoiceID = $_SESSION['retryInvoiceNumber'];
    $currUpdateInvoiceAmount = $_SESSION['retryInvoiceAmount'];
    $currUpdateInvoiceStatus = $_SESSION['retryInvoiceStatus'];
    $currUpdateInvoiceClient = $_SESSION['retryInvoiceClient'];
    $currUpdateInvoiceEmail = $_SESSION['retryInvoiceEmail'];
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark"> <!-- fancy in dark mode -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Manager - Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand font-monospace" href="index.php">Invoice Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="index.php"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav font-monospace">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add.php">Add</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-md-center font-monospace">
            <div class="p-1 m-5 col-6">
                <h1>
                    Update Invoice No.
                    <?php echo $currUpdateInvoiceID ?>:
                </h1>
                <form action="index.php" method="POST">
                    <div class="mb-3">
                        <label for="errors" class="form-label"><?= $_SESSION['errorString'] ?? '' ?></label>
                    </div>
                    <div class="mb-3">
                        <label for="orderIdTxt" class="form-label">Order ID</label>
                        <input type="text" class="form-control" name="number" aria-describedby="clientName" value="<?= $currUpdateInvoiceID ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="amountDueTxt" class="form-label">Amount Due</label>
                        <input type="text" class="form-control" name="amount" value="<?= $currUpdateInvoiceAmount ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="amountDueTxt" class="form-label">Invoice Status</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="pending" <?php if ($currUpdateInvoiceStatus === 'pending') echo 'checked'; ?> required />
                            <label class="form-check-label" for="inlineRadio1">Pending</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="paid" <?php if ($currUpdateInvoiceStatus === 'paid') echo 'checked'; ?> />
                            <label class="form-check-label" for="inlineRadio2">Paid</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="draft" <?php if ($currUpdateInvoiceStatus === 'draft') echo 'checked'; ?> />
                            <label class="form-check-label" for="inlineRadio2">Draft</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="clientNameTxt" class="form-label">Client Name</label>
                        <input type="text" class="form-control" name="client" value="<?= $currUpdateInvoiceClient ?? '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="clientEmailTxt" class="form-label">Client Email</label>
                        <input type="text" class="form-control" name="email" value="<?= $currUpdateInvoiceEmail ?? '' ?>" required>
                        <input type="hidden" name="pageOrigin" value="update">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="row justify-content-md-center">
            <div class="alert alert-success p-1 m-2 col-10">
                <h6 class="text-center font-monospace">Invoice Manager - Part 2, made by Olivia ***REMOVED***</h6>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>