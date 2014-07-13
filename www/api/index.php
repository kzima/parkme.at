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
		->select(
			'parking_locations.*', 
			Capsule::raw('COUNT(DISTINCT parked.id) AS parked'),
			Capsule::raw('COUNT(DISTINCT unparked.id) AS unparked'),
			Capsule::raw('MAX(unparked.created_at) AS last_reported_full'),
			Capsule::raw("ROUND(1000 * 6371 * ACOS(COS(RADIANS({$latitude})) * COS(RADIANS(parking_locations.latitude)) * COS(RADIANS(parking_locations.longitude) - RADIANS({$longitude})) + SIN(RADIANS({$latitude})) * SIN(RADIANS(parking_locations.latitude)))) AS distance")
		)
		->leftJoin('parked', function($join) use ($vehicleType) {
            $join->on('parking_locations.id', '=', 'parked.parking_location_id')->where('parked.vehicle_type', '=', $vehicleType);
        })
        ->leftJoin('unparked', function($join) use ($vehicleType) {
            $join->on('parking_locations.id', '=', 'unparked.parking_location_id')->where('unparked.vehicle_type', '=', $vehicleType);
        })
		->where($baysField, '>', 0)
		->groupBy('parking_locations.id')
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
			'location' => [
				'latitude' => $parkingLocation->latitude,
				'longitude' => $parkingLocation->longitude,
			],
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
				'lastReportedFull' => $parkingLocation->last_reported_full,
				'probabilityFull' => round($parkingLocation->unparked / $parkingLocation->parked, 2),
				'parked' => $parkingLocation->parked,
				'unparked' => $parkingLocation->unparked,
			],
		];

		// Iterate through parking times
		foreach ($parkingLocation->parkingTimes as $parkingTime) {
			// Append parking time to partial response
			$partialResponse['parkingTimes'][] = [
				'id' => $parkingTime->id,
				'startDay' => $parkingTime->start_day,
				'endDay' => $parkingTime->end_day,
				'startTime' => [
					'full' => date('g:ia', strtotime($parkingTime->start_time)),
					'hour' => date('g', strtotime($parkingTime->start_time)),
					'minutes' => date('i', strtotime($parkingTime->start_time)),
					'suffix' => date('a', strtotime($parkingTime->start_time)),
				],
				'endTime' => [
					'full' => date('g:iA', strtotime($parkingTime->end_time)),
					'hour' => date('g', strtotime($parkingTime->end_time)),
					'minutes' => date('i', strtotime($parkingTime->end_time)),
					'suffix' => date('a', strtotime($parkingTime->end_time)),
				],
				'operational' => $parkingTime->is_operational,
				'operationalToday' => $parkingTime->is_operational_today,
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
		'parking_location_id' => $id,
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
		'parking_location_id' => $id,
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