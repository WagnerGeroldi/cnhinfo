<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        flash('error', 'Faça login para acessar o painel administrativo.');
        redirect('login.php');
    }
}

function currentAdminName(): string
{
    return $_SESSION['admin_name'] ?? 'Administrador';
}

function generateRegistrationNumber(): string
{
    return 'CNHI-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

function normalizeText(string $value): string
{
    return trim(preg_replace('/\s+/', ' ', $value) ?? '');
}

function parseDate(?string $value): ?string
{
    if (!$value) {
        return null;
    }

    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y'];
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $value);
        if ($date instanceof DateTime) {
            return $date->format('Y-m-d');
        }
    }

    return null;
}
