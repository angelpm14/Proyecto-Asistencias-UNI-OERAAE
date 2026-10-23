<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
    exit();
}

include './header.php';
require './conexion.php';

// --- MEJORA DE SEGURIDAD ---
// Eliminar miembro de forma segura usando sentencias preparadas
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql_delete = "DELETE FROM MIEMBROS WHERE id_miembro = ?";
    $stmt = mysqli_prepare($cn, $sql_delete);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: miembros.php");
    exit();
}
function horasAcumuladas($id_miembro, $cn) {
    $sql = "SELECT entrada, salida FROM REGIST_ASIS WHERE id_miembro = ?";
    $stmt = mysqli_prepare($cn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_miembro);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $totalHoras = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['entrada']) && !empty($row['salida'])) {
            $entrada = new DateTime($row['entrada']);
            $salida = new DateTime($row['salida']);
            $interval = $entrada->diff($salida);
            $horas = $interval->h + ($interval->i / 60) + ($interval->s / 3600);
            if ($interval->d > 0) {
                $horas += $interval->d * 24;
            }
            $totalHoras += $horas;
        }
    }
    mysqli_stmt_close($stmt);
    return number_format($totalHoras, 2);
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Administrar Miembros</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>DNI</th>
                                <th>Rol</th>
                                <th>Área</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Especialidad</th>
                                <th>Turno</th>
                                <th>Horas Acum.</th>
                                <th>Horas Neces.</th>
                                <th>Estado</th>
                                <th class="no-export">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT m.*, 
                                           r.nombre AS rol_nombre,
                                           a.nombre AS area_nombre,
                                           t.nombre AS turno_nombre
                                    FROM MIEMBROS m
                                    LEFT JOIN ROL r ON m.id_rol = r.id_rol
                                    LEFT JOIN AREAS a ON m.id_area = a.id_area
                                    LEFT JOIN TURNO t ON m.id_turno = t.id_turno";
                            
                            $resul = mysqli_query($cn, $sql);

                            if (!$resul) {
                                echo "<tr><td colspan='15'>Error en la consulta: " . mysqli_error($cn) . "</td></tr>";
                            } else {
                                while ($row = mysqli_fetch_assoc($resul)) {
                                    $acum = horasAcumuladas($row['id_miembro'], $cn);
                                    $neces_str = str_replace(',', '', $row['hors_neces']);
                                    $neces = is_numeric($neces_str) ? floatval($neces_str) : 0;

                                    $estado = ($acum >= $neces && $neces > 0) 
                                        ? "<span class='badge badge-success'>✅ Completado</span>" 
                                        : "<span class='badge badge-warning'>⌛ Pendiente</span>";
                            ?>
                            
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_miembro']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($row['dni']); ?></td>
                                    <td><?php echo htmlspecialchars($row['rol_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['area_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email'] ?: 'Sin email'); ?></td>
                                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                                    <td><?php echo htmlspecialchars($row['especialidad']); ?></td>
                                    <td><?php echo htmlspecialchars($row['turno_nombre']); ?></td>
                                    <!-- Mostrar horas acumuladas en decimal -->
                                    <td><?php echo number_format((float)$acum, 2); ?></td>
                                    <td><?php echo number_format((float)$neces, 2); ?></td>
                                    <td><?php echo $estado; ?></td>
                                    
                                    <td>
                                        <a href="edit_miembros.php?id=<?php echo $row['id_miembro']; ?>" class="btn btn-success btn-sm">Editar</a>
                                        <a href="miembros.php?id=<?php echo $row['id_miembro']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar este miembro?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const acum = document.getElementById("hors_acum");
    const neces = document.getElementById("hors_neces");
    const progresoTexto = document.getElementById("progresoTexto");
    const barraProgreso = document.getElementById("barraProgreso");

    function actualizarProgreso() {
        let valAcum = parseFloat(acum.value) || 0;
        let valNeces = parseFloat(neces.value) || 0;

        // Texto
        progresoTexto.textContent = `Acumuladas: ${valAcum.toFixed(2)} / Necesarias: ${valNeces.toFixed(2)}`;

        // Porcentaje
        let porcentaje = (valNeces > 0) ? (valAcum / valNeces) * 100 : 0;
        if (porcentaje > 100) porcentaje = 100;

        // Barra
        barraProgreso.style.width = porcentaje.toFixed(2) + "%";
        barraProgreso.setAttribute("aria-valuenow", porcentaje.toFixed(2));
        barraProgreso.textContent = porcentaje.toFixed(2) + "%";

        // Colores dinámicos
        if (porcentaje < 50) {
            barraProgreso.className = "progress-bar bg-warning";
        } else if (porcentaje < 100) {
            barraProgreso.className = "progress-bar bg-info";
        } else {
            barraProgreso.className = "progress-bar bg-success";
        }
    }

    // Inicializa al cargar
    actualizarProgreso();

    // Escucha cambios
    acum.addEventListener("input", actualizarProgreso);
    neces.addEventListener("input", actualizarProgreso);
});
</script>