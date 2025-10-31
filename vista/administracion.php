<?php
session_start();

// Verificar que el usuario esté logueado y tenga rol correcto
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Docente - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-dark: #00312D;
            --accent-green: #BFF102;
        }

        .btn-accent {
            background-color: var(--accent-green);
            color: var(--primary-dark);
            border: none;
            font-weight: 600;
        }

        .btn-accent:hover {
            background-color: #a8d900;
        }

        .table-custom th {
            background-color: var(--primary-dark);
            color: white;
            border: none;
        }

        .stat-card {
            border-left: 4px solid var(--accent-green);
        }

        .btn-aprobar {
            background-color: #28a745;
            color: white;
        }

        .btn-rechazar {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00312D;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-person-badge"></i> Panel Docente
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-light me-3">
                    <i class="bi bi-person-circle"></i> Docente
                </span>
                <button class="btn btn-outline-warning btn-sm" onclick="cerrarSesion()">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </button>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-clipboard-check me-2"></i>Gestión de Solicitudes de Reservas
            </h1>
        </div>

        <!-- Estadísticas de Solicitudes -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted small">Solicitudes Pendientes</div>
                        <h3 class="text-warning">8</h3>
                        <small class="text-muted">Por revisar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted small">Aprobadas Hoy</div>
                        <h3 class="text-success">5</h3>
                        <small class="text-muted">Confirmadas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted small">Rechazadas Hoy</div>
                        <h3 class="text-danger">2</h3>
                        <small class="text-muted">No aprobadas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="text-muted small">Total Salas</div>
                        <h3 class="text-primary-dark">12</h3>
                        <small class="text-muted">Disponibles</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitudes Pendientes -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Solicitudes Pendientes de Aprobación
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Sala Solicitada</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Motivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Ana Rodríguez</strong><br>
                                    <small class="text-muted">ana@universidad.edu</small>
                                </td>
                                <td>
                                    <strong>Laboratorio B-205</strong><br>
                                    <small>Capacidad: 6 personas</small><br>
                                    <small>Equipamiento: Computadoras, Proyector</small>
                                </td>
                                <td>25/11/2024</td>
                                <td>14:00 - 16:00</td>
                                <td>Práctica de programación</td>
                                <td>
                                    <button class="btn btn-aprobar btn-sm me-1" onclick="aprobarSolicitud(1001)">
                                        <i class="bi bi-check-lg"></i> Aprobar
                                    </button>
                                    <button class="btn btn-rechazar btn-sm" onclick="rechazarSolicitud(1001)">
                                        <i class="bi bi-x-lg"></i> Rechazar
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Carlos Méndez</strong><br>
                                    <small class="text-muted">carlos@universidad.edu</small>
                                </td>
                                <td>
                                    <strong>Sala A-101</strong><br>
                                    <small>Capacidad: 4 personas</small><br>
                                    <small>Equipamiento: Proyector, Pizarra</small>
                                </td>
                                <td>26/11/2024</td>
                                <td>09:00 - 11:00</td>
                                <td>Estudio grupal</td>
                                <td>
                                    <button class="btn btn-aprobar btn-sm me-1" onclick="aprobarSolicitud(1002)">
                                        <i class="bi bi-check-lg"></i> Aprobar
                                    </button>
                                    <button class="btn btn-rechazar btn-sm" onclick="rechazarSolicitud(1002)">
                                        <i class="bi bi-x-lg"></i> Rechazar
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>María López</strong><br>
                                    <small class="text-muted">maria@universidad.edu</small>
                                </td>
                                <td>
                                    <strong>Auditorio Principal</strong><br>
                                    <small>Capacidad: 50 personas</small><br>
                                    <small>Equipamiento: Proyector, Sonido</small>
                                </td>
                                <td>27/11/2024</td>
                                <td>15:00 - 17:00</td>
                                <td>Presentación de proyecto</td>
                                <td>
                                    <button class="btn btn-aprobar btn-sm me-1" onclick="aprobarSolicitud(1003)">
                                        <i class="bi bi-check-lg"></i> Aprobar
                                    </button>
                                    <button class="btn btn-rechazar btn-sm" onclick="rechazarSolicitud(1003)">
                                        <i class="bi bi-x-lg"></i> Rechazar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Solicitudes Recientemente Procesadas -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>Solicitudes Recientemente Procesadas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Sala</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Estado</th>
                                <th>Fecha Decisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Juan Pérez</td>
                                <td>Sala C-102</td>
                                <td>24/11/2024</td>
                                <td>10:00 - 12:00</td>
                                <td><span class="badge bg-success">Aprobada</span></td>
                                <td>24/11/2024 09:15</td>
                            </tr>
                            <tr>
                                <td>Laura González</td>
                                <td>Laboratorio B-205</td>
                                <td>25/11/2024</td>
                                <td>16:00 - 18:00</td>
                                <td><span class="badge bg-success">Aprobada</span></td>
                                <td>24/11/2024 14:30</td>
                            </tr>
                            <tr>
                                <td>Pedro Sánchez</td>
                                <td>Auditorio Principal</td>
                                <td>26/11/2024</td>
                                <td>08:00 - 10:00</td>
                                <td><span class="badge bg-danger">Rechazada</span></td>
                                <td>24/11/2024 16:45</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function aprobarSolicitud(idSolicitud) {
            if (confirm('¿Estás seguro de que quieres APROBAR esta solicitud?')) {
                alert(`Solicitud #${idSolicitud} aprobada exitosamente`);
                // Aquí iría la lógica para actualizar el estado en la base de datos
                // recargarPagina(); // Opcional: recargar para ver cambios
            }
        }

        function rechazarSolicitud(idSolicitud) {
            const motivo = prompt('Por favor, ingresa el motivo del rechazo:');
            if (motivo !== null && motivo.trim() !== '') {
                alert(`Solicitud #${idSolicitud} rechazada. Motivo: ${motivo}`);
                // Aquí iría la lógica para actualizar el estado en la base de datos
                // recargarPagina(); // Opcional: recargar para ver cambios
            }
        }

        function cerrarSesion() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                window.location.href = 'login.php';
            }
        }

        function recargarPagina() {
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    </script>
</body>

</html>