<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = new Slim\Slim;
$app->add(new Slim\Middleware\ContentTypes);

$app->post('/locations', function() use ($app) {
	// Get request variables
	$body = $app->request->getBody();
	$vehicleType = isset($body['vehicleType']) ? $body['vehicleType'] : 'car';
	$latitude = isset($body['latitude']) ? $body['latitude'] : 0;
	$longitude = isset($body['longitude']) ? $body['longitude'] : 0;

	//
	/*$locations = Location::with('restrictions')
		->select('locations.*', DB::raw("ROUND(1000 * 6371 * ACOS(COS(RADIANS({$latitude})) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS({$longitude})) + SIN(RADIANS({$latitude})) * SIN(RADIANS(latitude)))) AS distance"))
		->orderBy('distance');

	echo '<pre>'; var_dump($locations); echo '</pre>'; die;*/


	// Get locations
	$locations = json_encode([
		'success' => true,
		'vehicleType' => $vehicleType,
		'currentLocation' => [
			'latitude' => $latitude,
			'longitude' => $longitude,
		],
		'currency' => 'aud',
		'distanceUnit' => 'm',
		'locations' => [
			[
				'id' => 1,
				'street' => 'Robertson Street',
				'suburb' => 'Fortitude Valley',
				'distance' => [
					'value' => 10,
				],
				'maximumStay' => [
					'value' => 4,
					'unit' => 'hr',
				],
				'rate' => [
					'operational' => true,
					'value' => 2.70,
				],
				'spaces' => 10,
				'restrictions' => [
					[
						'days' => 'Mon - Fri',
						'times' => '7am - 7pm',
					], 
					[
						'days' => 'Sat - Sun',
						'times' => '8am - 2pm',
					]
				],
				'status' => [
					'lastReportedFull' => '2014-07-12 10:34:21',
					'probabilityFull' => 0.52,
				],
			],
			[
				'id' => 2,
				'street' => 'Wellington Street',
				'suburb' => 'Woolloongabba',
				'distance' => [
					'value' => 12,
				],
				'maximumStay' => [
					'value' => 2,
					'unit' => 'hr',
				],
				'rate' => [
					'operational' => true,
					'value' => 1.50,
				],
				'spaces' => 12,
				'restrictions' => [
					[
						'days' => 'Mon - Fri',
						'times' => '10am - 2pm',
					]
				],
				'status' => [
					'lastReportedFull' => '2014-07-12 11:34:21',
					'probabilityFull' => 0.2,
				],
			],
		]
	]);

	// Prepare response
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->setBody($locations);
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