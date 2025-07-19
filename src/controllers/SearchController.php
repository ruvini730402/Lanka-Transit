<?php
/**
 * Search Controller
 * 
 * Handles bus search functionality
 */

require_once __DIR__ . '/../models/Route.php';
require_once __DIR__ . '/../models/Bus.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/Security.php';

class SearchController {
    private Route $routeModel;
    private Bus $busModel;
    
    public function __construct() {
        $this->routeModel = new Route();
        $this->busModel = new Bus();
    }
    
    /**
     * Search buses based on criteria
     * 
     * @return array
     */
    public function searchBuses(): array {
        try {
            // Get and sanitize input parameters
            $origin = Security::sanitizeInput($_GET['origin'] ?? '');
            $destination = Security::sanitizeInput($_GET['destination'] ?? '');
            $date = Security::sanitizeInput($_GET['date'] ?? '');
            $maxFare = Security::sanitizeInput($_GET['max_fare'] ?? '');
            
            // Validate input
            $validator = new Validator();
            $validator
                ->required('origin', $origin, 'Origin is required')
                ->required('destination', $destination, 'Destination is required')
                ->required('date', $date, 'Travel date is required')
                ->date('date', $date, 'Y-m-d', 'Please enter a valid date')
                ->custom('date', $date, function($value) {
                    return strtotime($value) >= strtotime('today');
                }, 'Travel date cannot be in the past');
            
            if (!empty($maxFare)) {
                $validator
                    ->numeric('max_fare', $maxFare, 'Maximum fare must be a valid number')
                    ->min('max_fare', $maxFare, 0, 'Maximum fare cannot be negative');
            }
            
            if ($validator->fails()) {
                return [
                    'success' => false,
                    'errors' => $validator->getErrors(),
                    'data' => []
                ];
            }
            
            // Find routes matching the criteria
            $routes = $this->routeModel->searchRoutes($origin, $destination);
            
            if (empty($routes)) {
                return [
                    'success' => true,
                    'message' => 'No routes found for the selected origin and destination.',
                    'data' => []
                ];
            }
            
            $results = [];
            
            foreach ($routes as $route) {
                // Get available buses for this route and date
                $buses = $this->busModel->getAvailableBuses($route['id'], $date);
                
                foreach ($buses as $bus) {
                    // Apply fare filter if specified
                    if (!empty($maxFare) && $bus['fare'] > $maxFare) {
                        continue;
                    }
                    
                    // Only include buses with available seats
                    if ($bus['available_seats'] > 0) {
                        $results[] = [
                            'route_id' => $route['id'],
                            'origin' => $route['origin'],
                            'destination' => $route['destination'],
                            'distance' => $route['distance'],
                            'estimated_duration' => $route['estimated_duration'],
                            'bus_id' => $bus['id'],
                            'bus_number' => $bus['bus_number'],
                            'bus_type' => $bus['bus_type'],
                            'operator_name' => $bus['operator_name'],
                            'departure_time' => $bus['departure_time'],
                            'arrival_time' => $bus['arrival_time'],
                            'fare' => $bus['fare'],
                            'total_seats' => $bus['total_seats'],
                            'available_seats' => $bus['available_seats'],
                            'amenities' => $bus['amenities'],
                            'schedule_id' => $bus['schedule_id']
                        ];
                    }
                }
            }
            
            // Sort results by departure time
            usort($results, function($a, $b) {
                return strcmp($a['departure_time'], $b['departure_time']);
            });
            
            return [
                'success' => true,
                'message' => count($results) > 0 ? 'Buses found successfully.' : 'No buses available for the selected criteria.',
                'data' => $results,
                'total' => count($results)
            ];
            
        } catch (Exception $e) {
            error_log("Error in SearchController::searchBuses - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while searching for buses. Please try again.',
                'data' => []
            ];
        }
    }
    
    /**
     * Get all origins for dropdown
     * 
     * @return array
     */
    public function getOrigins(): array {
        try {
            $origins = $this->routeModel->getAllOrigins();
            
            return [
                'success' => true,
                'data' => array_column($origins, 'origin')
            ];
            
        } catch (Exception $e) {
            error_log("Error in SearchController::getOrigins - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load origins',
                'data' => []
            ];
        }
    }
    
    /**
     * Get destinations for a given origin
     * 
     * @return array
     */
    public function getDestinations(): array {
        try {
            $origin = Security::sanitizeInput($_GET['origin'] ?? '');
            
            if (empty($origin)) {
                // Get all destinations if no origin specified
                $destinations = $this->routeModel->getAllDestinations();
            } else {
                // Get destinations for specific origin
                $destinations = $this->routeModel->getDestinationsForOrigin($origin);
            }
            
            return [
                'success' => true,
                'data' => array_column($destinations, 'destination')
            ];
            
        } catch (Exception $e) {
            error_log("Error in SearchController::getDestinations - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load destinations',
                'data' => []
            ];
        }
    }
}
