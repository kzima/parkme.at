<?php

require __DIR__.'/../vendor/autoload.php';

$app = new Slim\Slim();

$app->post('/locations', function() use ($app) {
	// Get post variables
	$vehicle = $app->request->post('vehicle', 'car');
	$latitude = $app->request->post('latitude');
	$longitude = $app->request->post('longitude');

	/*
		Everything in 500m or 1km, maximum 25 results
	*/

	// Get locations
	$locations = json_encode([
		'success' => true,
		'vehicle' => $vehicle,
		'currentLocation' => [
			'latitude' => $latitude,
			'longitude' => $longitude,
		],
		'currency' => 'aud',
		'distanceUnit' => 'km',
		'parkingDuration' => [0, 12],
		'cheapest' => [
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
				'cost' => [
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
				'cost' => [
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
		],
		'nearest' => [
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
				'cost' => [
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
				'cost' => [
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
	// Get post variables
	$vehicle = $app->request->post('vehicle', 'car');
	$latitude = $app->request->post('latitude');
	$longitude = $app->request->post('longitude');
});

$app->post('/locations/:id/unparked', function($id) use ($app) {
	// Get post variables
	$vehicle = $app->request->post('vehicle', 'car');
	$latitude = $app->request->post('latitude');
	$longitude = $app->request->post('longitude');
});

$app->run();

/*
Status 500
{
	"success": true|false,
	"message": "blah blah blah"
}
*/