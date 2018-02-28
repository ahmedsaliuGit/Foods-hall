<?php

/**
* App\Http\Controllers\Api
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;

/**
* UsersController class
 */
class UsersController extends Controller
{
    /**
     * Undocumented function
     *
     * @return User name
     */
	public function index()
	{
		$search = request('name');
		
		return User::where('name', 'LIKE', "$search%")
        ->take(5)
        ->pluck('name');
	}
}
