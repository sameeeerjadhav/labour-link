<?php
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    // Get user's location if provided
    $userLat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
    $userLng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
    
    // Fetch all labour users
    $stmt = $pdo->prepare("
        SELECT 
            id,
            full_name,
            username,
            phone,
            email,
            is_verified,
            created_at,
            latitude,
            longitude
        FROM users 
        WHERE role = 'labour'
        ORDER BY is_verified DESC, created_at DESC
    ");
    
    $stmt->execute();
    $labour = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate distance if user location is provided
    if ($userLat !== null && $userLng !== null) {
        foreach ($labour as &$person) {
            // For now, generate random nearby coordinates for demo
            // In production, you'd store actual user locations in the database
            $person['latitude'] = $userLat + (rand(-100, 100) / 1000); // ±0.1 degrees (~11km)
            $person['longitude'] = $userLng + (rand(-100, 100) / 1000);
            
            // Calculate distance using Haversine formula
            $person['distance'] = calculateDistance(
                $userLat, 
                $userLng, 
                $person['latitude'], 
                $person['longitude']
            );
        }
        
        // Sort by distance
        usort($labour, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
    }
    
    // Remove sensitive data
    foreach ($labour as &$person) {
        unset($person['email']);
        // Keep latitude/longitude for map features if needed
    }
    
    echo json_encode([
        'success' => true,
        'labour' => $labour,
        'count' => count($labour)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

/**
 * Calculate distance between two coordinates using Haversine formula
 * Returns distance in kilometers
 */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Earth's radius in kilometers
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;
    
    return round($distance, 2);
}
