<?php

class ParkingSeed {

    function run()
    {
    	// Parking location ids near QUT
    	$carParkingLocationIds = [98, 99, 101, 102, 103, 104, 105, 111, 112, 114, 115, 123, 761, 762, 763, 764, 765, 766, 767, 983];
    	$motorcycleParkingLocationIds = [136, 138, 163, 166, 351, 367, 371, 527, 654, 687, 768, 956, 1064];
    	$parkingLocationIds = array_merge($carParkingLocationIds, $motorcycleParkingLocationIds);

    	// Iterate through parking locations
	 	foreach ($parkingLocationIds as $parkingLocationId)	{
	 		// Prepare random number for parked and unparked records
	 		$parkedCount = rand(25, 50);
	 		$unparkedCount = rand(5, floor($parkedCount * 0.8));

	 		// Loop until parked count reached
	 		for ($i = 0; $i < $parkedCount; $i++) {
	 			// Determine vehicle type
	 			$vehicleType = in_array($parkingLocationId, $carParkingLocationIds) ? 'car' : 'motorcycle';

	 			// Create parked
	 			$parked = Parked::create([
	 				'parking_location_id' => $parkingLocationId,
	 				'vehicle_type' => $vehicleType,
	 				'latitude' => 0.0,
	 				'longitude' => 0.0,
	 			]);
	 		}

	 		// Loop until unparked count reached
	 		for ($i = 0; $i < $unparkedCount; $i++) {
	 			// Determine vehicle type
	 			$vehicleType = in_array($parkingLocationId, $carParkingLocationIds) ? 'car' : 'motorcycle';

	 			// Create parked
	 			$unparked = Unparked::create([
	 				'parking_location_id' => $parkingLocationId,
	 				'vehicle_type' => $vehicleType,
	 				'latitude' => 0.0,
	 				'longitude' => 0.0,
	 			]);
	 		}
	 	}
    }
}
