<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Reply;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

    public function store (Reply $reply)
    {
    	$reply->favorite();

    	if (request()->wantsJson()) {
    		return response(['status' => 'You favorited an reply']);
    	}

    	return back();
    }

    public function destroy(Reply $reply)
    {
    	$reply->unfavorite();

    	if (request()->wantsJson()) {
    		return response(['status' => 'You unfavorited an reply']);
    	}

    	return back();
    }
}
