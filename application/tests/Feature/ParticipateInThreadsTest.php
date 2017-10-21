<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParticipateInForumTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function unauthenticate_user_may_not_able_to_add_reply()
	{
		$this->withExceptionHandling()
			->post('/threads/some-channel/1/replies', [])
			->assertRedirect('login');
	}

	/** @test */
	public function an_authenticate_user_can_participate_in_forum_thread()
	{
		// $this->be(create('App\User'));
		$this->signIn();
		
		$thread = create('App\Thread');
		$reply = make('App\Reply'); // create() -> reply will create a database, and them save again. Make is better.
		$this->post($thread->path().'/replies', $reply->toArray());
		
		$this->assertDatabaseHas('replies', ['body' => $reply->body]);
		$this->assertEquals(1, $thread->fresh()->replies_count);
	}

	/** @test */
	public function a_reply_requires_a_body ()
	{
		$this->signIn();
		
		$thread = create('App\Thread');
		$reply = make('App\Reply', ['body' => null]);

		$this->withExceptionHandling()
			->post($thread->path().'/replies', $reply->toArray())
			->assertSessionHasErrors('body');
	}

	/** @test */
	public function unauthorized_users_can_not_delete_reply ()
	{

		$reply = create('App\Reply');

		$this->delete("/replies/{$reply->id}")
			->assertRedirect('login');

		$this->signIn()
			->delete("/replies/{$reply->id}")
			->assertStatus(403);
	}

	/** @test */
	public function authorized_users_can_delete_replies ()
	{
		$this->withoutExceptionHandling();
		$this->signIn();

		$reply = create('App\Reply', ['user_id' => auth()->id()]);

		$this->delete("/replies/{$reply->id}")->assertStatus(302);
		$this->assertDatabaseMissing('replies', ['id' => $reply->id]);

		$this->assertEquals(0, $reply->thread->fresh()->replies_count);
	}

	/** @test */
	public function  authorized_users_can_update_replies ()
	{
		$this->withoutExceptionHandling();
		$this->signIn();

		$reply = create('App\Reply', ['user_id' => auth()->id()]);
		$this->patch("/replies/{$reply->id}", ['body' => 'You have been change']);

		$this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => 'You have been change']);
	}

	/** @test */
	public function unauthorized_users_can_not_update_reply ()
	{

		$reply = create('App\Reply');

		$this->patch("/replies/{$reply->id}")
			->assertRedirect('login');

		$this->signIn()
			->patch("/replies/{$reply->id}", ['body' => 'Hello World'])
			->assertStatus(403);

		$this->assertDatabaseMissing('replies', ['id' => $reply->id, 'body' => 'Hello World']);
	}

	/** @test */
	public function replies_that_contains_spam_may_not_be_created ()
	{
		$this->withoutExceptionHandling();
		$this->signIn();
		
		$thread = create('App\Thread');
		$reply = make('App\Reply', [
			'body' => 'Yahoo Customer Support'
		]);

		$this->expectException(\Exception::class);

		$this->post($thread->path().'/replies', $reply->toArray());
	}


}
