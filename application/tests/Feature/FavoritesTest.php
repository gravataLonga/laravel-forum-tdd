<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoritesTes extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function guess_can_not_favorites_anything ()
	{
		$this->post('replies/1/favorites')
			->assertRedirect('/login');
	}

	/** @test */
	public function an_authenticate_user_can_favorite_any_reply ()
	{
		$this->withoutExceptionHandling();

		$this->signIn();
		
		$reply = create('App\Reply');

		$this->post('replies/' . $reply->id . '/favorites');

		$this->assertCount(1, $reply->favorites);
	}

	/** @test */
	public function an_authenticate_user_can_unfavorite_a_reply ()
	{
		$this->withoutExceptionHandling();

		$this->signIn();
		
		$reply = create('App\Reply');

		$reply->favorite();
		$this->delete('replies/' . $reply->id . '/favorites');

		$this->assertCount(0, $reply->favorites);
	}

	/** @test */
	public function an_authenticate_user_may_only_favorite_a_reply_once ()
	{
		$this->withoutExceptionHandling();

		$this->signIn();
		
		$reply = create('App\Reply');

		try {
			$this->post('replies/' . $reply->id . '/favorites');
			$this->post('replies/' . $reply->id . '/favorites');
		} catch (\Exception $e) {
			$this->fail('The user may not post twice');
		}
		

		$this->assertCount(1, $reply->favorites);
	}
}
