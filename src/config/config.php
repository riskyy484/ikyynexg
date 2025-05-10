<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pterodactyl_panel');

// Konfigurasi Pterodactyl
define('PTERODACTYL_DOMAIN', 'https://web.kennywebsite.web.id');
define('PTERODACTYL_API_KEY', 'ptla_lbjLyBh3ZPn8aA6d7S89lNdK0945BYA5IkbS0BHOcd1');
define('PTERODACTYL_CLIENT_KEY', 'ptlc_cKlbFnYHK1lJqu1rEE7uTIGKwwKasJk6wTDmQlRpYdq');

// Konfigurasi Aplikasi
define('APP_ROOT', dirname(dirname(__DIR__)));
define('URL_ROOT', 'http://localhost/pterodactyl-panel');
define('SITE_NAME', 'Kenny Pterodactyl Panel');

// Memulai session
session_start();