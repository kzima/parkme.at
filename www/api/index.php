<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$app = new Slim\Slim;
$app->add(new Slim\Middleware\ContentTypes);

$app->post('/locations', function() use ($app) {
	// Get request variables
	$body = $app->request->getBody();
	$vehicleType = isset($body['vehicleType']) ? $body['vehicleType'] : 'car';
	$latitude = isset($body['latitude']) ? $body['latitude'] : 0;
	$longitude = isset($body['longitude']) ? $body['longitude'] : 0;

	// Determine parking bays field based on vehicle type
	$baysField = $vehicleType === 'car' ? 'vehicle_bays' : 'motorcycle_bays';

	// Get nearest 20 parking locations
	$parkingLocations = ParkingLocation::with('parkingTimes')
		->select('parking_locations.*', Capsule::raw("ROUND(1000 * 6371 * ACOS(COS(RADIANS({$latitude})) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS({$longitude})) + SIN(RADIANS({$latitude})) * SIN(RADIANS(latitude)))) AS distance"))
		->where($baysField, '>', 0)
		->orderBy('distance')
		->take(20)
		->get();

	// Prepare response
	$response = [
		'success' => true,
		'currency' => 'aud',
		'symbol' => '$',
	];

	// Iterate through locations
	foreach ($parkingLocations as $parkingLocation) {
		// Prepare partial response
		$partialResponse = [
			'id' => $parkingLocation->id,
			'street' => $parkingLocation->street,
			'suburb' => $parkingLocation->suburb,
			'parkingBays' => $parkingLocation->vehicleBays($vehicleType),
			'distance' => [
				'value' => $parkingLocation->distance,
				'unit' => 'm',
			],
			'maximumStay' => [
				'value' => $parkingLocation->maximum_stay,
				'unit' => 'hr',
			],
			'rate' => [
				'operational' => $parkingLocation->is_restricted,
				'value' => $parkingLocation->currentVehicleRate($vehicleType),
				'currency' => 'aud',
				'symbol' => '$',
				'period' => 'hr',
			],
			'parkingTimes' => [],
			'status' => [
				'lastReportedFull' => false,
				'probabilityFull' => 0.5,
			],
		];

		// Iterate through parking times
		foreach ($parkingLocation->parkingTimes as $parkingTime) {
			// Append parking time to partial response
			$partialResponse['parkingTimes'][] = [
				'id' => $parkingTime->id,
				'days' => sprintf('%s - %s', $parkingTime->start_day, $parkingTime->end_day),
				'times' => sprintf('%s - %s', date('g:iA', strtotime($parkingTime->start_time)), date('g:iA', strtotime($parkingTime->end_time))),
				'operational' => $parkingTime->is_operational,
			];
		}

		// Append partial response to complete response
		$response['parkingLocations'][] = $partialResponse;
	}

	// Prepare response
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->setBody(json_encode($response));
});

$app->post('/locations/:id/parked', function($id) use ($app) {
	// Get request variables
	$body = $app->request->getBody();
	$vehicleType = isset($body['vehicleType']) ? $body['vehicleType'] : 'car';
	$latitude = isset($body['latitude']) ? $body['latitude'] : 0;
	$longitude = isset($body['longitude']) ? $body['longitude'] : 0;

	// Create new parked record
	$parked = Parked::create([
		'location_id' => $id,
		'vehicle_type' => $vehicleType,
		'latitude' => $latitude,
		'longitude' => $longitude,
	]);

	// Prepare response
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->setBody(json_encode([
		'success' => true,
	]));
});

$app->post('/locations/:id/unparked', function($id) use ($app) {
	// Get request variables
	$body = $app->request->getBody();
	$vehicleType = isset($body['vehicleType']) ? $body['vehicleType'] : 'car';
	$latitude = isset($body['latitude']) ? $body['latitude'] : 0;
	$longitude = isset($body['longitude']) ? $body['longitude'] : 0;

	// Create new unparked record
	$unparked = Unparked::create([
		'location_id' => $id,
		'vehicle_type' => $vehicleType,
		'latitude' => $latitude,
		'longitude' => $longitude,
	]);

	// Prepare response
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->setBody(json_encode([
		'success' => true,
	]));
});

$app->run();

/*
Status 500
{
	"success": true|false,
	"message": "blah blah blah"
}
*/