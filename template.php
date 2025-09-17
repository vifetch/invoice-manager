<?php

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

function allFilter() // return all as true on Index.php
{
    return true; // jank, fix later
}


/* pull current php file name then filter it using
filter_sanitize_email since it works and is easier than
 making a custom filter? kind of jank, then uppercase first letter */
$page = ucwords(filter_var(($_SERVER["PHP_SELF"]), FILTER_SANITIZE_EMAIL));


/* switch case that checks current page name to decice
which callback function is used, previously i had the
functions stored individually in each .php page but
it was causing a lot of issues with redeclaration and includes */

switch ($page) {
    case 'Pending.php':
        $currPageFilter = $pendingFilter;
        break;
    case 'Paid.php':
        $currPageFilter = $paidFilter;
        break;
    case 'Draft.php':
        $currPageFilter = $draftFilter;
        break;
    case 'Index.php':
        $currPageFilter->allFilter;
        // how to make $currPageFilter point to desired filter func??
        break;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Invoice Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
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


    <div class="container text-center">
        <div class="row">
            <div class="col">
                <h1>Invoice List (<?php
                // crap way to make it display the current page in Header
                    if ($page == 'Index.php') {
                        echo ucwords("All");
                    } else {
                        echo str_replace('.php', '', $page);
                    }
                    ?>): </h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">
                                <a>Order No.</a>
                            </th>
                            <th scope="col">
                                <a>Amount</a>
                            </th>
                            <th scope="col">
                                <a>Status</a>
                            </th>
                            <th scope="col">
                                <a>Client Name</a>
                            </th>
                            <th scope="col">
                                <a>Email</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_filter($invoices, $currPageFilter) as $entry) : ?>
                            <tr scope="row">
                                <td><?php echo ucwords($entry['number']); ?></td>
                                <td>$<?php echo ucwords($entry['amount']); ?>.00</td>
                                <td><?php echo ucwords($entry['status']); ?></td>
                                <td><?php echo ucwords($entry['client']); ?></td>
                                <td><?php echo ($entry['email']); ?></td>
                            </tr>
                    </tbody>
                <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <div class="alert alert-success text-center p-2 m-2" role="alert">
        Invoice Manager in php made by Olivia ***REMOVED***
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>