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
	protected $appends = ['is_operational', 'is_peak', 'is_off_peak', 'is_operational_today'];

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
		// Get numerical representation for today, start day and end day
		$today = date('N');
		$startDay = date('N', strtotime("{$this->start_day} this week"));
		$endDay = date('N', strtotime("{$this->end_day} this week"));

		// Check if today falls between start and end days
		if ( ! ($startDay <= $today && $today <= $endDay)) {
			return false;
		}

		// Get current time, start time and end time
		$time = time();
		$startTime = strtotime($this->start_time);
		$endTime = strtotime($this->end_time);

		// Check if time falls between start and end time
		if ( ! ($startTime <= $time && $time <= $endTime)) {
			return false;
		}

		return true;
	}
	
	/**
	 * Check if parking time is operational for peak hours.
	 * Off peak time is for meter spaces that operate outside of:
	 * Monday - Friday, 7pm - 10pm
	 * Saturday - Sunday, 7am - 7pm
	 *
	 * @return bool
	 */
	public function getIsPeakAttribute()
	{
		// Get numerical representation for today, weekdays and weekend
		$today = date('N');
		$weekdays = [1, 2, 3, 4, 5];
		$weekend = [6, 7];

		// Get current time, weekday off-peak start time and end time and weekend off-peak start time and end time
		$time = time();
		$weekdayStartTime = strtotime('19:00:00');
		$weekdayEndTime = strtotime('22:00:00');
		$weekendStartTime = strtotime('07:00:00');
		$weekendEndTime = strtotime('19:00:00');

		// Check if today falls on a weekday and between off peak times
		if (in_array($today, $weekdays) && ($weekdayStartTime <= $time && $time <= $weekdayEndTime)) {
			return false;
		}

		// Check if today falls on a weekend and between off peak times
		if (in_array($today, $weekend) && ($weekendStartTime <= $time && $time <= $weekendEndTime)) {
			return false;
		}

		return true;
	}

	/**
	 * Check if parking time is operational for off peak hours.
	 * Off peak time is for meter spaces that operate inside of:
	 * Monday - Friday, 7pm - 10pm
	 * Saturday - Sunday, 7am - 7pm
	 *
	 * @return bool
	 */
	public function getIsOffPeakAttribute()
	{
		// Get numerical representation for today, weekdays and weekend
		$today = date('N');
		$weekdays = [1, 2, 3, 4, 5];
		$weekend = [6, 7];

		// Get current time, weekday off-peak start time and end time and weekend off-peak start time and end time
		$time = time();
		$weekdayStartTime = strtotime('19:00:00');
		$weekdayEndTime = strtotime('22:00:00');
		$weekendStartTime = strtotime('07:00:00');
		$weekendEndTime = strtotime('19:00:00');

		// Check if today falls on a weekday and between off peak times
		if (in_array($today, $weekdays) && ($weekdayStartTime <= $time && $time <= $weekdayEndTime)) {
			return true;
		}

		// Check if today falls on a weekend and between off peak times
		if (in_array($today, $weekend) && ($weekendStartTime <= $time && $time <= $weekendEndTime)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if parking time is operational today.
	 *
	 * @return bool
	 */
	public function getIsOperationalTodayAttribute()
	{
		// Get numerical representation for today, start day and end day
		$today = date('N');
		$startDay = date('N', strtotime("{$this->start_day} this week"));
		$endDay = date('N', strtotime("{$this->end_day} this week"));

		// Check if today falls between start and end days
		if ($startDay <= $today && $today <= $endDay) {
			return true;
		}

		return false;
	}

}
