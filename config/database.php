<?php

declare(strict_types=1);

const DB_HOST = 'localhost';
const DB_NAME = 'cnhinfoc_cnhi';
const DB_USER = 'cnhinfoc_admin';
const DB_PASS = 'Unisoft1997#';
const APP_NAME = 'CNHI';
const APP_URL = '';

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}
