<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Location extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'locations';

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = ['is_restricted'];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');
	

	/**
	 * Get location parking restrictions.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function restrictions() 
	{
		return $this->hasMany('Restriction');
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
	 * Check if location has active parking restriction.
	 * 
	 * @return bool
	 */
	public function getIsRestrictedAttribute()
	{
		// Iterate through restrictions
		foreach ($this->restrictions as $restriction) {
			// Check if restriction applicable on current day / time
			if ($restriction->isApplicable()) {
				return true;
			}
		}

		return false;
	}

}
