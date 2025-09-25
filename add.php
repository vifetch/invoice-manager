<?php 
session_start();

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
        <div class="row justify-content-md-center font-monospace">
            <div class="p-1 m-5 col-6">
                <h1>
                    Add New Invoice:
                </h1>
                <form action="index.php" method="POST">
                    <div class="mb-3">
                        <label for="orderIdTxt" class="form-label">Order ID</label>
                        <input type="text" class="form-control" name="number" aria-describedby="clientName">
                    </div>
                    <div class="mb-3">
                        <label for="amountDueTxt" class="form-label">Amount Due</label>
                        <input type="text" class="form-control" name="amount">
                    </div>
                    <div class="mb-3">
                        <label for="invoiceStatusTxt" class="form-label">Invoice Status (pending, paid, draft)</label>
                        <input type="text" class="form-control" name="status">
                    </div>
                    <div class="mb-3">
                        <label for="clientNameTxt" class="form-label">Client Name</label>
                        <input type="text" class="form-control" name="client">
                    </div>
                    <div class="mb-3">
                        <label for="clientEmailTxt" class="form-label">Client Email</label>
                        <input type="email" class="form-control" name="email">
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