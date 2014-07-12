<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Users table migration.
 */
class UserMigration 
{
    function run()
    {
        Capsule::schema()->dropIfExists('users');

        Capsule::schema()->create('users', function($table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->timestamps();
        });
    }
}
