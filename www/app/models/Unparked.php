<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Unparked extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'unparked';


	/**
	 * Get location.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function location()
	{
		return $this->belongsTo('Location');
	}
	
}
