<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadTest extends TestCase
{
	use RefreshDatabase;

	protected $thread;

	public function setUp()
	{
		parent::setUp();
		$thread = create('App\Thread');
		$this->thread = $thread;
	}

	/** @test */
	public function thread_has_a_creator()
	{
		$this->assertInstanceOf(User::class, $this->thread->creator);
	}

	/** @test */
	public function an_thread_can_make_a_string_path ()
	{
		$thread = create('App\Thread');

		$this->assertEquals('/threads/' . $thread->channel->slug.'/'.$thread->id, $thread->path());
	}

	/** @test */
	public function a_threads_has_replies()
	{
		$this->assertInstanceOf(Collection::class, $this->thread->replies);
	}

	/**	@test */
	public function a_thread_can_be_reply()
	{
		$this->thread->addReply([
			'body' => 'body',
			'user_id' => 1
		]);

		$this->assertCount(1, $this->thread->replies);
	}

	/** @test */
	function a_threads_belongs_to_a_channel ()
	{
		$thread = create('App\Thread');
		
		$this->assertInstanceOf('App\Channel', $thread->channel);	
	}

	/**	@test */
	public function a_thread_can_be_subscribe()
	{
		// Given we have a thread
		$thread = create('App\Thread');

		// When the user subscribe a thread
		$thread->subscribe($userId = 1);

		// Then we must be able to fetch all the subscribe threads for this user.
		$this->assertCount(1, 
			$thread->subscriptions()->where('user_id', 1)->get()
		);
	}

	/** @test */
	public function a_thread_can_be_unsubscribe ()
	{
		// Given we have a thread
		$thread = create('App\Thread');

		// When the user subscribe a thread
		$thread->subscribe($userId = 1);

		// And the user unsubscribe a thread
		$thread->unsubscribe($userId);

		// Then we must be able to fetch all the subscribe threads for this user.
		$this->assertCount(0, 
			$thread->subscriptions()->where('user_id', 1)->get()
		);
	}
}
