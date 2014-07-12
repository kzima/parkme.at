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
	protected $appends = ['is_timed'];

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
	 * Check if location has active parking time.
	 * 
	 * @return bool
	 */
	public function getIsTimedAttribute()
	{
		// Iterate through restrictions
		foreach ($this->parkingTimes as $parkingTime) {
			// Check if parking time operational for current day / time
			if ($parkingTimes->is_operational) {
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
		// Rate per hour for meter spaces that operate 7pm -10pm Mon – Fri and 7am – 7pm Saturday, Sunday
		return null;
	}

}
