<?php
// This file completely bypasses Laravel's CSRF protection
// It's a standalone script in the public folder

// Include Laravel's autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Simple validation
if (!isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit;
}

try {
    // Get the user from database
    $user = App\Models\User::where('email', $input['email'])->first();
    
    if ($user && password_verify($input['password'], $user->password)) {
        // Login successful
        // Start a session for the user
        session_start();
        $_SESSION['user_id'] = $user->id;
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'facility_id' => $user->facility_id
            ]
        ]);
    } else {
        // Login failed
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}