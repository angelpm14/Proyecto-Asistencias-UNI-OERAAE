<?php
ob_start();
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("location:main.php");
    exit();
}

require './conexion.php';
include './header.php';

// --- Si viene id_regist (editar) ---
$id_regist = $_GET['id'] ?? null;
$id_miembro = $_GET['miembro'] ?? null;

$registro = null;
if ($id_regist) {
    $stmt = mysqli_prepare($cn, "SELECT id_regist, id_miembro, justifc FROM REGIST_ASIS
                                WHERE id_regist = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_regist);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $registro = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// --- Procesar envío del formulario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_regist_post = $_POST['id_regist'] ?? null;
    $id_miembro_post = $_POST['id_miembro'] ?? null;
    $justifc = trim($_POST['justifc'] ?? '');

    if ($id_regist_post) {
        // Editar justificación existente
        $stmt = mysqli_prepare($cn, "UPDATE REGIST_ASIS SET justifc = ? WHERE id_regist = ?");
        mysqli_stmt_bind_param($stmt, "si", $justifc, $id_regist_post);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif ($id_miembro_post) {
        // Crear justificación si faltó (sin entrada ni salida)
        $stmt = mysqli_prepare($cn, "INSERT INTO REGIST_ASIS (id_miembro, entrada, salida, dni, justifc)
                                     VALUES (?, NULL, NULL, (SELECT dni FROM MIEMBROS WHERE id_miembro = ?), ?)");
        mysqli_stmt_bind_param($stmt, "iis", $id_miembro_post, $id_miembro_post, $justifc);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: regist_asist.php?msg=justificado");
    exit();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">
                <?php echo $id_regist ? "Editar Justificación" : "Registrar Justificación"; ?>
            </h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <?php if ($id_regist): ?>
                            <input type="hidden" name="id_regist" value="<?php echo $registro['id_regist']; ?>">
                        <?php elseif ($id_miembro): ?>
                            <input type="hidden" name="id_miembro" value="<?php echo htmlspecialchars($id_miembro); ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Motivo de Justificación</label>
                            <textarea name="justifc" class="form-control" rows="5" required><?php echo htmlspecialchars($registro['justifc'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Guardar Justificación</button>
                        <a href="regist_asist.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ob_end_flush(); // Envia todo al navegador ?>
<?php include './footer.php'; ?>
