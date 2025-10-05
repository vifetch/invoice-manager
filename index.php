<?php
/* index.php */

include 'data.php';
session_start();

/* functions */

function pendingFilter($invoices) {
    if ($invoices['status'] == 'pending') { // callback func for array_filter
        return true;                        // with 'pending' status
    }
}

function paidFilter($invoices) {
    if ($invoices['status'] == 'paid') {
        return true;
    }
}

function draftFilter($invoices) {
    if ($invoices['status'] == 'draft') {
        return true;
    }
}

function allFilter($invoices) { // use or gates to filter all 3 invoice types
    if ($invoices['status'] == 'pending' || 'paid' || 'draft') {
        return true;
    }
}

function deleteInvoiceFromValue($invoices, $key, $val) {
    foreach ($invoices as $subKey => $subArray) {
        if ($subArray[$key] == $val) {
            unset($invoices[$subKey]);
        }
    }
    return $invoices;
} // https://stackoverflow.com/a/4466437

function validateStatus(string $val) {
    if ($val == 'pending' || $val == 'paid' || $val == 'draft') {
        return $val;
    }
    return null;
}

/* first run code (populate pageOrigin and sessionInvoice if empty) */

if (empty($_POST['pageOrigin'])) {
    $_POST['pageOrigin'] = 'home';
}
if (empty($_SESSION['sessionInvoice'])) {
    $_SESSION['sessionInvoice'] = $invoices;
} // if $_SESSION['sessionInvoice'] is already populated, do not overwrite / erase user entries

/* empty session variables responsible for keeping track of invoice data entered that is not yet in the main array */
$_SESSION['retryInvoiceNumber'] = "";
$_SESSION['retryInvoiceAmount'] = "";
$_SESSION['retryInvoiceStatus'] = "";
$_SESSION['retryInvoiceClient'] = "";
$_SESSION['retryInvoiceEmail'] = "";
$_SESSION['errorString'] = "";

/* sort all invoices data alphabetically by order id */
$orders = array_column($invoices, 'number');
array_multisort($orders, SORT_ASC, SORT_STRING, $invoices);
/* check post value on invoiceStatus to determine which filter to use */
$status = $_GET['invoiceStatus'] ?? 'all';
$pageFunc = $status . 'filter';

/* only execute the large if/else checks if there is post data with a pageOrigin of add or update */
if ($_POST['pageOrigin'] == 'add' || $_POST['pageOrigin'] == 'update') {
    /* sanitize user input using server-side, null if not valid */
    $_POST['number'] = filter_var($_POST['number'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z]+$/']]);
    $_POST['amount'] = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT);
    $_POST['status'] = filter_var($_POST['status'], FILTER_CALLBACK, ['options' => 'validateStatus']);
    $_POST['client'] = filter_var($_POST['client'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z\s]{1,255}$/']]);
    $_POST['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    /* check which post data for updating/adding invoices is valid, add error msg to $errorString */
    if (empty($_POST['number'])) {
        $errorString = 'ERROR: Invoice ID invalid';
    } elseif (empty($_POST['amount'])) {
        $errorString = 'ERROR: Invoice Amount invalid';
    } elseif (empty($_POST['status'])) {
        $errorString = 'ERROR: Invoice status invalid';
    } elseif (empty($_POST['client'])) {
        $errorString = 'ERROR: Invoice client name invalid';
    } elseif (empty($_POST['email'])) {
        $errorString = 'ERROR: Invoice email invalid';
    }
    /* if there were errors, save relevant post data to session storage as I found this was
    the most elegant way to return the data back to the respective form without using ajax? */
    if (!empty($errorString)) {
        $_SESSION['errorString'] = $errorString;
        $_SESSION['retryInvoiceNumber'] = $_POST['number'];
        $_SESSION['retryInvoiceAmount'] = $_POST['amount'];
        $_SESSION['retryInvoiceStatus'] = $_POST['status'];
        $_SESSION['retryInvoiceClient'] = $_POST['client'];
        $_SESSION['retryInvoiceEmail'] = $_POST['email'];

        if ($_POST['pageOrigin'] == 'update') {
            header('Location: /update.php', true, 302);
            exit;
        } elseif ($_POST['pageOrigin'] == 'add') {
            header('Location: /add.php', true, 302);
            exit;
        }
    }
    /* if there were no errors, add post data to new array, delete old invoice and combine to multidimensional array */ elseif (isset($_POST['number'], $_POST['amount'], $_POST['status'], $_POST['client'], $_POST['email'])) {
        $newInvoice = [
            'number' => $_POST['number'],
            'amount' => $_POST['amount'],
            'status' => $_POST['status'],
            'client' => $_POST['client'],
            'email' => $_POST['email'],
        ];

        $_SESSION['sessionInvoice'] = deleteInvoiceFromValue($_SESSION['sessionInvoice'], 'number', $_POST['number']);
        $_SESSION['sessionInvoice'][] = $newInvoice;
    }
}
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