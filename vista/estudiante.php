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
    <title>Dashboard Estudiante - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================== Estilos tal cual tu versión original ================== */
        :root {
            --color-oscuro: #00312D;
            --color-verde-claro: #72BF00;
            --color-verde-medio: #3A7817;
            --color-fondo: #EAFDE7;
        }

        body {
            background: var(--color-fondo);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .barra-superior {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0, 49, 45, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .enlace-nav {
            color: var(--color-oscuro);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .enlace-nav:hover {
            background: var(--color-fondo);
            color: var(--color-verde-medio);
        }

        .enlace-nav.activo {
            background: var(--color-fondo);
            color: var(--color-verde-medio);
            font-weight: 600;
        }

        .boton-cerrar-sesion {
            background: var(--color-verde-claro);
            color: white;
            padding: 10px 28px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .boton-cerrar-sesion:hover {
            background: var(--color-verde-medio);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(114, 191, 0, 0.3);
        }

        .banner-principal {
            background: linear-gradient(135deg, var(--color-oscuro) 0%, var(--color-verde-medio) 100%);
            border-radius: 20px;
            padding: 30px;
            margin: 30px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 49, 45, 0.15);
        }

        .titulo-banner {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .subtitulo-banner {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .contenedor-principal {
            padding: 0 30px 50px;
        }

        .seccion-izquierda {
            flex: 1;
        }

        .seccion-derecha {
            width: 400px;
            margin-left: 30px;
        }

        .titulo-seccion {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--color-oscuro);
        }

        .tarjeta-sala {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid transparent;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 49, 45, 0.08);
        }

        .tarjeta-sala:hover {
            border-color: var(--color-verde-claro);
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(114, 191, 0, 0.15);
        }

        .nombre-sala {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--color-oscuro);
        }

        .info-sala {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .info-sala i {
            color: var(--color-verde-medio);
            width: 25px;
        }

        .boton-solicitar {
            background: var(--color-verde-claro);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .boton-solicitar:hover {
            background: var(--color-verde-medio);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(114, 191, 0, 0.3);
        }

        .tarjeta-solicitud {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 49, 45, 0.08);
        }

        .solicitud-pendiente {
            border-left-color: #ffc107;
        }

        .solicitud-aprobada {
            border-left-color: #28a745;
        }

        .solicitud-rechazada {
            border-left-color: #dc3545;
        }

        .info-solicitud h5 {
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--color-oscuro);
        }

        .info-solicitud small {
            color: #666;
            display: block;
            margin-bottom: 4px;
        }

        .etiqueta-estado {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }

        .estado-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .estado-aprobado {
            background: #d4edda;
            color: #155724;
        }

        .estado-rechazado {
            background: #f8d7da;
            color: #721c24;
        }

        .boton-cancelar {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .boton-cancelar:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .badge-disponible {
            background: var(--color-fondo);
            color: var(--color-verde-medio);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            background: var(--color-oscuro);
            color: white;
            border-radius: 16px 16px 0 0;
        }

        @media (max-width: 992px) {
            .contenedor-principal {
                flex-direction: column;
            }

            .seccion-derecha {
                width: 100%;
                margin-left: 0;
                margin-top: 30px;
            }

            .banner-principal {
                padding: 30px;
                margin: 20px;
            }

            .titulo-banner {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>

    <!-- Barra Superior -->
    <div class="barra-superior">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold fs-5" style="color: var(--color-oscuro);">Sistema de Reservas</span>
                </div>

                <div class="d-none d-md-flex gap-2">
                    <a href="#salas" class="enlace-nav activo">
                        <i class="fas fa-door-open me-1"></i>Solicitar Sala
                    </a>
                    <a href="#solicitudes" class="enlace-nav">
                        <i class="fas fa-history me-1"></i>Mis Solicitudes
                    </a>
                </div>

                <button class="btn boton-cerrar-sesion" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </button>
            </div>
        </div>
    </div>

    <!-- Banner Principal -->
    <div class="banner-principal">
        <h4 class="titulo-banner">Hola, Estudiante</h4>
        <p class="subtitulo-banner">Solicita tus salas de estudio y sigue el estado de tus reservas</p>
    </div>

    <!-- Contenido Principal -->
    <div class="contenedor-principal d-flex">

        <!-- Sección Izquierda: Solicitar Salas -->
        <div class="seccion-izquierda" id="salas">
            <h2 class="titulo-seccion">
                <i class="fas fa-door-open me-2"></i>Solicitar Sala de Estudio
            </h2>
        </div>

        <!-- Sección Derecha: Estado de Solicitudes -->
        <div class="seccion-derecha" id="solicitudes">
            <h2 class="titulo-seccion">
                <i class="fas fa-history me-2"></i>Estado de Mis Solicitudes
            </h2>
        </div>
    </div>

    <!-- Modal para Solicitud -->
    <div class="modal fade" id="modalSolicitud" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Solicitar Sala: <span id="modalSalaNombre"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSolicitud">
                        <div class="mb-3">
                            <label class="form-label">Fecha deseada</label>
                            <input type="date" class="form-control" id="fechaSolicitud" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horario de inicio</label>
                            <input type="time" class="form-control" id="horaInicio" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horario de fin</label>
                            <input type="time" class="form-control" id="horaFin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Motivo de la reserva</label>
                            <textarea class="form-control" id="motivoSolicitud" rows="3"
                                placeholder="Describe el propósito de tu reserva..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número de personas</label>
                            <input type="number" class="form-control" id="numeroPersonas" min="1" max="8" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn boton-solicitar" onclick="enviarSolicitud()">
                        <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ================== JS dinámico manteniendo tu diseño ==================

        let salaSeleccionada = '';
        let capacidadSala = 0;
        let idSalaSeleccionada = 0;

        document.addEventListener('DOMContentLoaded', () => {
            cargarSalas();
            cargarMisReservas();
        });

        function cargarSalas() {
            fetch('../controlador/EstudianteController.php?action=salas')
                .then(res => res.json())
                .then(data => {
                    if (data.success) mostrarSalas(data.salas);
                })
                .catch(err => console.error(err));
        }

        function mostrarSalas(salas) {
            const cont = document.querySelector('.seccion-izquierda');
            const titulo = cont.querySelector('.titulo-seccion');
            cont.innerHTML = '';
            cont.appendChild(titulo);

            salas.forEach(sala => {
                const div = document.createElement('div');
                div.className = 'tarjeta-sala';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h3 class="nombre-sala mb-0">${sala.nombre}</h3>
                        <span class="badge-disponible">${sala.estado === 'activa' ? 'Disponible' : 'No disponible'}</span>
                    </div>
                    <p class="info-sala"><i class="fas fa-users"></i><span>Capacidad: ${sala.capacidad} personas</span></p>
                    <p class="info-sala"><i class="fas fa-desktop"></i><span>Equipamiento: ${sala.equipamiento || 'No especificado'}</span></p>
                    <p class="info-sala mb-0"><i class="fas fa-info-circle"></i><span>Tipo: ${sala.tipo_sala}</span></p>
                    <button class="btn boton-solicitar" onclick="abrirModalSolicitud('${sala.nombre}', ${sala.capacidad}, ${sala.id_sala})">
                        <i class="fas fa-paper-plane me-2"></i>Solicitar Esta Sala
                    </button>
                `;
                cont.appendChild(div);
            });
        }

        function abrirModalSolicitud(nombre, capacidad, id) {
            salaSeleccionada = nombre;
            capacidadSala = capacidad;
            idSalaSeleccionada = id;
            document.getElementById('modalSalaNombre').textContent = nombre;
            document.getElementById('numeroPersonas').max = capacidad;
            document.getElementById('formSolicitud').reset();
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fechaSolicitud').min = hoy;
            new bootstrap.Modal(document.getElementById('modalSolicitud')).show();
        }

        function enviarSolicitud() {
            const fecha = document.getElementById('fechaSolicitud').value;
            const inicio = document.getElementById('horaInicio').value;
            const fin = document.getElementById('horaFin').value;
            const motivo = document.getElementById('motivoSolicitud').value;
            const personas = document.getElementById('numeroPersonas').value;

            if (!fecha || !inicio || !fin || !motivo || !personas) return alert('Completa todos los campos');
            if (personas > capacidadSala) return alert(`Máximo ${capacidadSala} personas`);
            if (fin <= inicio) return alert('La hora de fin debe ser mayor a la de inicio');

            const formData = new FormData();
            formData.append('id_sala', idSalaSeleccionada);
            formData.append('fecha', fecha);
            formData.append('hora_inicio', inicio);
            formData.append('hora_fin', fin);
            formData.append('motivo', motivo);

            fetch('../controlador/EstudianteController.php?action=solicitar', {
                method: 'POST', body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('¡Reserva creada!');
                        bootstrap.Modal.getInstance(document.getElementById('modalSolicitud')).hide();
                        cargarMisReservas();
                    } else alert(data.mensaje);
                }).catch(err => console.error(err));
        }

        function cargarMisReservas() {
            fetch('../controlador/EstudianteController.php?action=mis_reservas')
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;
                    const cont = document.querySelector('.seccion-derecha');
                    const titulo = cont.querySelector('.titulo-seccion');
                    cont.innerHTML = '';
                    cont.appendChild(titulo);

                    if (data.reservas.length === 0) {
                        cont.innerHTML += '<p class="text-muted">No tienes reservas aún</p>';
                        return;
                    }

                    data.reservas.forEach(r => cont.appendChild(crearTarjetaReserva(r)));
                }).catch(err => console.error(err));
        }

        function crearTarjetaReserva(r) {
            const div = document.createElement('div');
            let clase = '', etiqueta = '', texto = '';

            switch (r.estado) {
                case 'confirmada': clase = 'solicitud-pendiente'; etiqueta = 'estado-aprobado'; texto = 'Confirmada'; break;
                case 'en_curso': clase = 'solicitud-aprobada'; etiqueta = 'estado-aprobado'; texto = 'En Curso'; break;
                case 'cancelada': clase = 'solicitud-rechazada'; etiqueta = 'estado-rechazado'; texto = 'Cancelada'; break;
                default: clase = 'solicitud-pendiente'; etiqueta = 'estado-pendiente'; texto = 'Pendiente';
            }

            div.className = `tarjeta-solicitud ${clase}`;
            div.innerHTML = `
                <div class="info-solicitud">
                    <h5>${r.nombre_sala}</h5>
                    <small><i class="fas fa-calendar-alt me-1"></i>${r.fecha}</small>
                    <small><i class="fas fa-clock me-1"></i>${r.hora_inicio} - ${r.hora_fin}</small>
                    <small><i class="fas fa-users me-1"></i>${r.numero_personas} personas</small>
                    <small><i class="fas fa-comment me-1"></i>${r.motivo}</small>
                    <span class="etiqueta-estado ${etiqueta}">${texto}</span>
                </div>
                ${r.estado === 'confirmada' || r.estado === 'pendiente' ? `<button class="boton-cancelar mt-2" onclick="cancelarReserva(${r.id_reserva})"><i class="fas fa-times me-1"></i>Cancelar</button>` : ''}
            `;
            return div;
        }

        function cancelarReserva(id) {
            if (!confirm('¿Deseas cancelar esta reserva?')) return;
            const formData = new FormData();
            formData.append('id_reserva', id);

            fetch('../controlador/EstudianteController.php?action=cancelar', {
                method: 'POST', body: formData
            }).then(res => res.json())
                .then(data => {
                    if (data.success) cargarMisReservas();
                    else alert(data.mensaje);
                }).catch(err => console.error(err));
        }

        function cerrarSesion() {
            if (confirm('¿Deseas cerrar sesión?')) window.location.href = '../controlador/EstudianteController.php?action=logout';
        }
    </script>

</body>

</html>
