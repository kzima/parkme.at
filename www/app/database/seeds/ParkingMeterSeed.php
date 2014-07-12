<?php

class ParkingMeterSeed {

    function run()
    {
    	// Open CSV file
		$file = fopen(__DIR__ . '/data/dataset_parking_meter.csv', 'r');
		$count = 0;
		
		// Iterate through CSV per line
		while ( ! feof($file)) {
			// Increment count
            $count += 1;

            // Get data for current CSV line
			$data = fgetcsv($file);

            // Skip first line
            if ($count === 1) {
            	continue;
            }

			// Create parking location
            $parkingLocation = ParkingLocation::create([
            	'type' => 'on_street',
				'meter_number' => $data[0],
				'street' => ucwords(strtolower($data[2])),
				'suburb' => ucwords(strtolower($data[3])),
				'locality' => $data[11],
				'maximum_stay' => $data[4],
				'vehicle_bays' => $data[12],
				'motorcycle_bays' => $data[13],
	            'vehicle_peak_rate' => $data[9],
	            'vehicle_off_peak_rate' => $data[10],
	            'motorcycle_rate' => $data[14],
	            'tariff_zone' => $data[8],
	            'restrictions' => ucwords(strtolower($data[5])),
	            'latitude' => $data[16],
	            'longitude' => $data[15],
            ]);

			// Cleanse operational day
		    if (strcasecmp($data[6], 'daily') === 0) {
		    	$data[6] = 'MON-SUN';
		    }	
		    elseif (strcasecmp($data[6], 'mon-fri') === 0) {
		    	$data[6] = 'MON-FRI';
		    }
		    elseif (strcasecmp($data[6], 'sat-sun') === 0) {
		    	$data[6] = 'SAT-SUN';
		    }

			// Cleanse operational time
		    $data[7] = preg_replace('/\s?(&|,)\s?/', ',', $data[7]);

			// Split data on comma separator    
		    $parkingTimes = explode(',', $data[7]);

		    // Parse parking times
		    foreach ($parkingTimes as $parkingTime) {
		    	// Append operational day to operational time if day range not specified
		    	if ( ! preg_match('/\(.*\)/', $parkingTime)) {
		    		$parkingTime .= '(' . $data[6] . ')';
		    	}

		    	// Extract start and end day and time
		    	preg_match('/^(?P<start_time>\d+(:\d+)?(AM|PM))\-(?P<end_time>\d+(:\d+)?(AM|PM))\((?P<start_day>[A-Z]{3})(\-(?P<end_day>[A-Z]{3}))?\)$/i', $parkingTime, $matches);

		    	// Check for end day and use start date when not specified
		    	if ( ! isset($matches['end_day'])) {
		    		$matches['end_day'] = $matches['start_day'];
		    	}

		    	// Create parking time
		    	$parkingTime = ParkingTime::create([
		    		'parking_location_id' => $parkingLocation->id,
			    	'start_day' => ucfirst(strtolower($matches['start_day'])),
			    	'end_day' => ucfirst(strtolower($matches['end_day'])),
			    	'start_time' => date('H:i:s', strtotime($matches['start_time'])),
			    	'end_time' => date('H:i:s', strtotime($matches['end_time'])),
		    	]);
		    }
		}

		// Close CSV file
		fclose($file);
    }
}
