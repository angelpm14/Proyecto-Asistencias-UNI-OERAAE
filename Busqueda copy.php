<?php
session_start();
if(!isset($_SESSION['id_usuario'])){
    header("location:main.php");
} else {
?>
<?php include './header.php'; ?>
<?php require './conexion.php'; ?>

<div class="content-wrapper" style="min-height: 1306.94px;">
    <section class="content">
        <div class="container-fluid">
            <br>
            <h2 class="text-center display-4">Buscar</h2>
<!--
            <form action="enhanced-results.html">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Result Type:</label>
                                    <select class="select2 select2-hidden-accessible" multiple="" data-placeholder="Any" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                        <option>Text only</option>
                                        <option>Images</option>
                                        <option>Video</option>
                                    </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="2" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--multiple" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false"><ul class="select2-selection__rendered"><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="Any" style="width: 476.225px;"></li></ul></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Sort Order:</label>
                                    <select class="select2 select2-hidden-accessible" style="width: 100%;" data-select2-id="3" tabindex="-1" aria-hidden="true">
                                        <option selected="" data-select2-id="5">ASC</option>
                                        <option>DESC</option>
                                    </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="4" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-98e9-container"><span class="select2-selection__rendered" id="select2-98e9-container" role="textbox" aria-readonly="true" title="ASC">ASC</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Order By:</label>
                                    <select class="select2 select2-hidden-accessible" style="width: 100%;" data-select2-id="6" tabindex="-1" aria-hidden="true">
                                        <option selected="" data-select2-id="8">Title</option>
                                        <option>Date</option>
                                    </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="7" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-1ysk-container"><span class="select2-selection__rendered" id="select2-1ysk-container" role="textbox" aria-readonly="true" title="Title">Title</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <input type="search" class="form-control form-control-lg" placeholder="Type your keywords here" value="Lorem ipsum">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>            
-->

            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <form action="" method="post">
                        <div class="input-group">
                            <input type="search" name="busqueda" class="form-control form-control-lg" placeholder="Escriba nombre o DNI aquí" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <?php
            if (isset($_POST['busqueda'])) {
                $busqueda = $_POST['busqueda'];
                // Ajusta la consulta según tu estructura de tablas
                $sql = "SELECT a.dni, u.nombre, t.hora_inicio, t.hora_fin, a.hora_entrada
                        FROM asistencia a
                        INNER JOIN usuarios u ON a.dni = u.dni
                        INNER JOIN TURNO t ON u.id_turno = t.id_area
                        WHERE u.nombre LIKE '%$busqueda%' OR a.dni LIKE '%$busqueda%'
                        ORDER BY a.hora_entrada DESC";
                $result = mysqli_query($cn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-bordered table-striped">';
                    echo '<thead><tr>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Horario</th>
                            <th>Hora Entrada</th>
                            <th>Estado</th>
                          </tr></thead><tbody>';
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Determinar estado
                        $estado = "Faltó";
                        if ($row['hora_entrada']) {
                            $hora_entrada = strtotime($row['hora_entrada']);
                            $hora_inicio = strtotime($row['hora_inicio']);
                            if ($hora_entrada <= $hora_inicio) {
                                $estado = "Puntual";
                            } else {
                                $estado = "Tardanza";
                            }
                        }
                        echo '<tr>
                                <td>'.$row['nombre'].'</td>
                                <td>'.$row['dni'].'</td>
                                <td>'.$row['hora_inicio'].' - '.$row['hora_fin'].'</td>
                                <td>'.$row['hora_entrada'].'</td>
                                <td>'.$estado.'</td>
                              </tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<div class="alert alert-warning">No se encontraron resultados.</div>';
                }
            }
            ?>
        </div>
    </section>
</div>

<?php include './footer.php'; ?>
<?php } ?>