<?php

namespace Tests\Feature;

use App\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function guest_may_not_create_threads ()
	{
		$this->get('/threads/create')
			->assertRedirect('login');

		// Hit endpoint to create new endpoint
		$this->post('/threads')
			->assertRedirect('login');
	}

	/** @test */
	public function an_authenticate_user_can_create_a_thread()
	{
		// Authenticate user
		$this->signIn();

		// Hit endpoint to create new endpoint
		$thread = make('App\Thread');
		$response = $this->post('/threads', $thread->toArray());

		// Then when we visit threads we can see our title.
		$response = $this->get($response->headers->get('Location'))
			->assertSee($thread->title)
			->assertSee($thread->body);
	}

	/** @test */
	public function a_thread_requires_a_title ()
	{
		$this->publishThread(['title' => null])
			->assertSessionHasErrors('title');
	}

	/** @test */
	public function a_thread_requires_a_body ()
	{
		$this->publishThread(['body' => null])
			->assertSessionHasErrors('body');
	}

	/** @test */
	public function a_thread_requires_a_valid_channel ()
	{
		$channels = factory('App\Channel', 2)->create();

		$this->publishThread(['channel_id' => null])
			->assertSessionHasErrors('channel_id');

		$this->publishThread(['channel_id' => 999])
			->assertSessionHasErrors('channel_id');
	}

	/** @test */
	public function unauthorized_users_may_not_delete_threads ()
	{
		$thread = create('App\Thread');
		$response = $this->delete($thread->path())->assertRedirect('login');

		$this->signIn();
		$this->delete($thread->path())->assertStatus(403);
	}

	/** @test */
	public function authorized_user_can_delete_threads ()
	{
		$this->signIn();

		$thread = create('App\Thread', ['user_id' => auth()->id()]);
		$reply = create('App\Reply', ['thread_id' => $thread->id]);

		$response = $this->json('DELETE', $thread->path());
 
		$response->assertStatus(204);

		$this->assertDatabaseMissing('threads', ['id' => $thread->id ]);
		$this->assertDatabaseMissing('replies', ['id' => $reply->id ]);
		$this->assertEquals(0, Activity::count());
		// $this->assertDatabaseMissing('activities', [
		// 	'subject_id' => $thread->id,
		// 	'subject_type' => get_class($thread)
		// ]);

		// $this->assertDatabaseMissing('activities', [
		// 	'subject_id' => $reply->id,
		// 	'subject_type' => get_class($reply)
		// ]);
	}

	public function publishThread ($overrides = [])
	{
		$this->signIn();

		$thread = make('App\Thread', $overrides);
		return $this->post('/threads', $thread->toArray());
	}
}
