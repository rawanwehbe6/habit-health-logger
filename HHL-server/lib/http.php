<?php

declare(strict_types=1);

/**
 * Send a JSON response and exit.
 *
 * @param mixed $data
 * @param int   $statusCode
 */
function jsonResponse($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($data);
    exit;
}

/**
 * Read JSON request body as array.
 *
 * @return array<string,mixed>
 */
function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Common handler for unexpected server errors.
 *
 * @param \Throwable 
 * @param string    
 */
function serverError(Throwable $e, string $publicMessage = 'Server error'): void
{
    jsonResponse([
        'status'  => 'error',
        'error'   => $publicMessage,
        'message' => $e->getMessage(),
    ], 500);
}
