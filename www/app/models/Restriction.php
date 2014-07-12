<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Restriction extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'restrictions';

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = ['is_applicable'];

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');


	/**
	 * Get location.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function location()
	{
		return $this->belongsTo('Location');
	}

	/**
	 * Check if parking restriction is applicable on current day / time.
	 * 
	 * @return bool
	 */
	public function getIsApplicableAttribute()
	{
		// 
	}
	
}
