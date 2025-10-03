<?php
include 'data.php';

/* invoice filters */

function pendingFilter($invoices)
{
    if ($invoices['status'] == 'pending') { // callback func for array_filter
        return true;                        // with 'pending' status
    }
}

function paidFilter($invoices)
{
    if ($invoices['status'] == 'paid') {
        return true;
    }
}

function draftFilter($invoices)
{
    if ($invoices['status'] == 'draft') {
        return true;
    }
}

function allFilter($invoices)
{ // use or gates to filter all 3 invoice types
    if ($invoices['status'] == 'pending' || 'paid' || 'draft') {
        return true;
    }
}

/* update invoice */

function deleteInvoiceFromValue($invoices, $key, $val)
{
    foreach ($invoices as $subKey => $subArray) {
        if ($subArray[$key] == $val) {
            unset($invoices[$subKey]);
        }
    }
    return $invoices;
} // https://stackoverflow.com/a/4466437


$orders = array_column($invoices, 'number'); // save number column from invoices
/* sort all invoices data alphabetically by order id (check DB to make
sure this doesn't take up a bunch of resources */
array_multisort($orders, SORT_ASC, SORT_STRING, $invoices);

$status = $_GET['invoiceStatus'] ?? 'all';
$pageFunc = $status . 'filter';
/* check post value on invoiceStatus to determine which button was pressed /
which filter to use */

session_start();

if (!isset($_SESSION['sessionInvoice'])) {
    $_SESSION['sessionInvoice'] = $invoices;
} // if $_SESSION['sessionInvoice'] is already populated, do not overwrite / erase user entries

if (isset($_POST['number'], $_POST['amount'], $_POST['status'], $_POST['client'], $_POST['email'])) {

    $_SESSION['sessionInvoice'] = deleteInvoiceFromValue($_SESSION['sessionInvoice'], 'number', $_POST['number']);

    $newInvoice = [
        'number' => $_POST['number'],
        'amount' => $_POST['amount'],
        'status' => $_POST['status'],
        'client' => $_POST['client'],
        'email' => $_POST['email'],
    ];

    $_SESSION['sessionInvoice'][] = $newInvoice;
}


/* if there are post values corresponding to a new invoice, add to $newInvoice
array, then append to $_SESSION['sessionInvoice'] to be displayed all together */


?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark"> <!-- fancy in dark mode -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Manager</title>
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
        <div class="row justify-content-md-center">
            <div class="col p-1 m-5 col-6 text-center font-monospace">
                <h1 class="">Invoice List</h1>
                <label for="buttonSortByLabel" class="form-label">Sort by:</label>
                <?php $statuses = ['all', 'draft', 'pending', 'paid']; // change from hardcoded? 
                ?>
                <form method="get">
                    <div class="btn-group">
                        <?php foreach ($statuses as $status): ?>
                            <button type="submit" name="invoiceStatus" value="<?= $status ?>"
                                class="btn <?= $currentStatus === $status ? 'active' : '' ?>">
                                <?= ucfirst($status) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-md-center">

            <div id="tableContainer" class="col col-10 table-responsive-xs">
                <table class="table table-hover font-monospace">
                    <thead>
                        <tr class="table-active">
                            <th scope="col">
                                <a>Order ID</a>
                            </th>
                            <th scope="col">
                                <a>Amount Due</a>
                            </th>
                            <th scope="col">
                                <a>Invoice Status</a>
                            </th>
                            <th scope="col">
                                <a>Client Name</a>
                            </th>
                            <th scope="col" id="clientEmail">
                                <a>Client Email</a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_filter($_SESSION['sessionInvoice'], $pageFunc) as $entry) : ?>
                            <tr scope="row">
                                <td><?php echo ucwords($entry['number']); ?></td>
                                <td>$<?php echo ucwords($entry['amount']); ?>.<span class="centsDisplay text-white-50">00</span></td>
                                <td><?php echo ucwords($entry['status']); ?></td>
                                <td><?php echo ucwords($entry['client']); ?></td>
                                <td id="clientEmail">
                                    <a class="link-secondary" href="mailto:<?php echo ($entry['email']); ?>"><?php echo ($entry['email']); ?></a>
                                </td>
                                <td>
                                    <form action="update.php" method="get" class="btn-group-sm">
                                        <button name="invoiceToUpdate" class="btn btn-outline-secondary" value="<?php echo ucwords($entry['number']); ?>">Edit</button>
                                        <button name="invoiceToDelete" class="btn btn-outline-secondary" value="<?php echo ucwords($entry['number']); ?>">Delete</button>
                                    </form> <!-- serialize() instead ? -->
                                </td>
                            <?php endforeach; ?>
                            </tr>
                    </tbody>

                </table>
            </div>
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