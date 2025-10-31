<?php
session_start();
require_once '../controlador/AuthController.php';

// Crear instancia del controlador
$auth = new AuthController();

// Llamar al método login
$auth->login();
