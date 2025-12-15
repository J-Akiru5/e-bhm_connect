<?php
// Save chatbot conversation history to DB (placeholder)
// TODO: implement DB save
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');
echo json_encode(['status'=>'ok']);
