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

			// Create location
			$location = new Location;
			$location->type = 'on_street';
			$location->meter_number = $data[0];
			$location->street = ucwords(strtolower($data[2]));
			$location->suburb = ucwords(strtolower($data[3]));
			$location->locality = $data[11];
			$location->maximum_stay = $data[4];
			$location->vehicle_bays = $data[12];
			$location->motorcycle_bays = $data[13];
            $location->vehicle_peak_rate = $data[9];
            $location->vehicle_off_peak_rate = $data[10];
            $location->motorcycle_rate = $data[14];
            $location->tariff_zone = $data[8];
            $location->restrictions = ucwords(strtolower($data[5]));
            $location->latitude = $data[16];
            $location->longitude = $data[15];
            $location->save();

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
		    $restrictions = explode(',', $data[7]);

		    // Parse restrictions
		    foreach ($restrictions as $restriction) {
		    	// Append operational day to operational time if day range not specified
		    	if ( ! preg_match('/\(.*\)/', $restriction)) {
		    		$restriction .= '(' . $data[6] . ')';
		    	}

		    	// Extract start and end day and time
		    	preg_match('/^(?P<start_time>\d+(:\d+)?(AM|PM))\-(?P<end_time>\d+(:\d+)?(AM|PM))\((?P<start_day>[A-Z]{3})(\-(?P<end_day>[A-Z]{3}))?\)$/i', $restriction, $matches);

		    	// Check for end day and use start date when not specified
		    	if ( ! isset($matches['end_day'])) {
		    		$matches['end_day'] = $matches['start_day'];
		    	}

		    	// Create restriction
		    	$restriction = new Restriction;
		    	$restriction->location_id = $location->id;
		    	$restriction->start_day = ucfirst(strtolower($matches['start_day']));
		    	$restriction->end_day = ucfirst(strtolower($matches['end_day']));
		    	$restriction->start_time = date('H:i:s', strtotime($matches['start_time']));
		    	$restriction->end_time = date('H:i:s', strtotime($matches['end_time']));
		    	$restriction->save();
		    }
		}

		// Close CSV file
		fclose($file);
    }
}
