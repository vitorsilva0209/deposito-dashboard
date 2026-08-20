<nav class="navbar navbar-expand-lg navbar-dark bg-danger">

    <div class="container-fluid">

        <a class="navbar-brand" href="dashboard.php">
            Depósito Brasil
        </a>

        <div class="d-flex align-items-center">

            <span class="text-white me-3">
                Olá,
                <?php echo htmlspecialchars($_SESSION["nome"]); ?>
            </span>

            <a href="logout.php" class="btn btn-light">
                Sair
            </a>

        </div>

    </div>

</nav>