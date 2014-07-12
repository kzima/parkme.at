<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ParkingTime extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'parking_times';

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = ['is_operational'];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');


	/**
	 * Get parking location.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function location()
	{
		return $this->belongsTo('ParkingLocation');
	}

	/**
	 * Check if parking time is operational based on current day / time.
	 * 
	 * @return bool
	 */
	public function getIsOperationalAttribute()
	{
		// Get current timestamp
		$timestamp = time();
		
		// Prepare restriction start and end timestamps
		$start1 = strtotime("{$this->start_day} this week {$this->start_time}");
		$start2 = strtotime("{$this->start_day} this week {$this->end_time}");
		$end1 = strtotime("{$this->end_day} this week {$this->start_time}");
		$end2 = strtotime("{$this->end_day} this week {$this->end_time}");

		return (($start1 <= $timestamp && $timestamp <= $start2) || ($end1 <= $timestamp && $timestamp <= $end2));
	}
	
}
