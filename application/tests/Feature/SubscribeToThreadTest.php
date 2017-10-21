<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscribeToThreadTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function a_user_can_subscribe_to_thread ()
	{
		$this->withoutExceptionHandling();

		// Give we have a thread and an authenticate user
		$this->signIn();
		$thread = create('App\Thread');

		// When a user subscribe a thread
		$this->post($thread->path() . '/subscriptions');
		$this->assertCount(1, $thread->subscriptions);
	}

	/** @test */
	public function a_user_can_unsubscribe_from_thread ()
	{
		$this->withoutExceptionHandling();

		// Give we have a thread and an authenticate user
		$this->signIn();
		$thread = create('App\Thread');

		$thread->subscribe();

		// When a user subscribe a thread
		$this->delete($thread->path() . '/subscriptions');
		$this->assertCount(0, $thread->subscriptions);
		// Then the user may receive an alert.
	}
}
