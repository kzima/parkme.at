<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ParkingLocation extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'parking_locations';

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = ['is_timed', 'is_timed_peak', 'is_timed_off_peak'];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');
	

	/**
	 * Get location parking times.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function parkingTimes() 
	{
		return $this->hasMany('ParkingTime');
	}

	/**
	 * Get location parked records.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function parked()
	{
		return $this->hasMany('Parked');
	}

	/**
	 * Get location unparked records.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function unparked()
	{
		return $this->hasMany('Unparked');
	}

	/**
	 * Check if parking location has active parking time.
	 * 
	 * @return bool
	 */
	public function getIsTimedAttribute()
	{
		// Iterate through parking times
		foreach ($this->parkingTimes as $parkingTime) {
			// Check if parking time is currently operational
			if ($parkingTime->is_operational) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if parking location has active peak parking time.
	 * Peak time is for meter spaces that operate outside of:
	 * Monday - Friday, 7pm - 10pm
	 * Saturday - Sunday, 7am - 7pm
	 * 
	 * @return bool
	 */	
	public function getIsTimedPeakAttribute()
	{
		// Iterate through parking times
		foreach ($this->parkingTimes as $parkingTime) {
			// Check if parking time is currently operational and covers peak hours
			if ($parkingTime->is_operational && $parkingTime->is_peak) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if parking location has active off peak parking time.
	 *
 	 * @return bool
	 */
	public function getIsTimedOffPeakAttribute()
	{
		// Iterate through parking times
		foreach ($this->parkingTimes as $parkingTime) {
			// Check if parking time is currently operational and covers off peak hours
			if ($parkingTime->is_operational && $parkingTime->is_off_peak) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get number of parking bays for specified vehicle type.
	 *
	 * @param string $vehicleType
	 * @return int
	 */
	public function vehicleBays($vehicleType = 'car')
	{
		return $vehicleType === 'car' ? $this->vehicle_bays : $this->motorcycle_bays;
	}

	/**
	 * Get current rate for specified vehicle type.
	 *
	 * @param string $vehicleType
	 * @return int
	 */
	public function currentVehicleRate($vehicleType = 'car')
	{	
		// Check if vehicle type is car and parking location has active peak parking time
		if ($vehicleType === 'car' && $this->is_timed_peak) {
			return floatval($this->vehicle_peak_rate);
		}
		// Check if vehicle type is car and parking location has active off peak parking time
		elseif ($vehicleType === 'car' && $this->is_timed_off_peak) {
			return floatval($this->vehicle_off_peak_rate);
		}

		// Check if vehicle type is motorcycle and parking location has active parking time
		if ($vehicleType === 'motorcycle' && $this->is_timed) {
			return floatval($this->motorcycle_rate);
		}

		return 0;
	}

}
