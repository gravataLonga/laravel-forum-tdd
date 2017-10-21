<?php

namespace App\Filters;

use App\User;
use App\Filters\Filters;

/**
* Filter ThreadFilter
*/
class ThreadFilter extends Filters
{
	/**
	 * Filters Availables
	 */
	protected $filters = ['by', 'popular', 'unanswered'];

	/** My Filter */
	protected function by($username) {
		$user = User::where('name', $username)->firstOrFail();
		return $this->builder->where('user_id', $user->id);
	}

	protected function popular()
	{
		$this->builder->orderBy('replies_count', 'desc');
	}

	protected function unanswered()
	{
		$this->builder->where('replies_count', 0);
	}
}