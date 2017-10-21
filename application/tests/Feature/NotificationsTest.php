<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
	use RefreshDatabase;

	public function setUp()
	{
		parent::setUp();
		$this->withoutExceptionHandling();
		$this->signIn();
	}

	/** @test */
	public function a_notification_is_prepared_when_a_subscribed_thread_receive_a_reply ()
	{
		$thread = create('App\Thread')->subscribe();
		$this->assertCount(0, auth()->user()->notifications); //Sanity check
		$thread
			->addReply([
				'user_id' => auth()->id(),
				'body' => 'Lorem ipsum'
			]);

		// Then the user may receive an alert. But not who leave the reply.
		$this->assertCount(0, auth()->user()->fresh()->notifications);

		$thread
			->addReply([
				'user_id' => create('App\User')->id,
				'body' => 'Lorem ipsum'
			]);

		$this->assertCount(1, auth()->user()->fresh()->notifications);
	}

	/** @test */
	public function a_user_can_fetch_their_unread_notification ()
	{
		$notification = create('Illuminate\Notifications\DatabaseNotification');
		$response = $this->getJson("/profile/" . auth()->user()->name. "/notifications")->json();
		$this->assertCount(1, $response);
	} 

	/** @test */
	public function a_user_can_mark_a_notification_as_read()
	{
		$notification = create('Illuminate\Notifications\DatabaseNotification');
		$user = auth()->user();
		$this->assertCount(1, $user->unreadNotifications);

		$userName = $user->name;
		$notificationId = $user->unreadNotifications->first()->id;
		$this->delete("/profile/{$userName}/notifications/{$notificationId}");
		$this->assertCount(0, $user->fresh()->unreadNotifications);
	}
}
