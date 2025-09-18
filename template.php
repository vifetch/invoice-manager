<?php
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

function indexFilter($invoices) { // use or gates to filter all 3 invoice types
    if ($invoices['status'] == 'pending' || 'paid' || 'draft') {
        return true;
    }
}

/* pull current php file name then filter it using filter_sanitize_email since it 
works and easier than making a custom filter? capitalize first letter and replace '.php' with nothing*/
$page = str_replace('.php', '', ucwords(filter_var(($_SERVER["PHP_SELF"]), FILTER_SANITIZE_EMAIL)));

$pageFunc = $page.'filter'; // find function starting in page name, set to $pageFunc

$orders = array_column($invoices, 'number'); // save number column from invoices

/* sort all invoices data alphabetically by order id (check DB to make
sure this doesn't take up a bunch of resources */
array_multisort($orders, SORT_ASC, SORT_STRING, $invoices);

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
                        <a class="nav-link" href="draft.php">Draft</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pending.php">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="paid.php">Paid</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container">
        <div class="row justify-content-md-center">
            <div id="tableContainer" class="col col-10 table-responsive-xs">
                <h1 class="m-3 font-monospace">Invoice List (<?php
                    if ($page == 'Index') {
                        echo "All";
                    } else {
                        echo str_replace('.php', '', $page);
                    }
                    ?>): </h1>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_filter($invoices, $pageFunc) as $entry) : ?>
                            <tr scope="row">
                                <td><?php echo ucwords($entry['number']); ?></td>
                                <td>$<?php echo ucwords($entry['amount']); ?>.<span class="centsDisplay text-white-50">00</span></td>
                                <td><?php echo ucwords($entry['status']); ?></td>
                                <td><?php echo ucwords($entry['client']); ?></td>
                                <td id="clientEmail"> <a class="link-secondary" href="mailto:<?php echo ($entry['email']);?>"><?php echo ($entry['email']); ?></a></td>
                                <?php endforeach; ?>
                            </tr>
                    </tbody>

                </table>
            </div>
                <div class="alert alert-success p-1 m-2 col-10">
                    <h6 class="text-center font-monospace">Invoice Manager - Part 1, made by Olivia ***REMOVED***</h6>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>