<?php 
include_once "header.php";
require_once "../models/database.php";
?>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white text-center rounded-top-4">
                        <h4 class="mb-0">📦 Importar Productos desde Excel</h4>
                    </div>

                    <div class="card-body p-4">
                        <form action="../controller/ProductoController.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo_excel" class="form-label">Selecciona el archivo Excel</label>
                                <input type="file" name="archivo_excel" id="archivo_excel" class="form-control" accept=".xlsx,.xls" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="importar" class="btn btn-success">
                                    📤 Importar Productos
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer text-muted text-center small">
                        Asegúrate de que el archivo esté en formato .xlsx y siga la estructura correcta.
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

<?php include_once "footer.php"; ?>