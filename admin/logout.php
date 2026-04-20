<?php
require_once __DIR__ . '/../config/functions.php';
session_unset();
session_destroy();
session_start();
flash('success', 'Você saiu do painel com sucesso.');
redirect('login.php');
