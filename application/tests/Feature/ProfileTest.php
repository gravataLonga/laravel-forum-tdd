<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
	use RefreshDatabase;

	protected $user;

	public function setUp ()
	{
		parent::setUp();
		$this->withoutExceptionHandling();
		$this->user = create('App\User', ['name' => 'JohnDoe']); // Because Mr. Gregorio D'Amore get error.
	}

	/** @test */
	public function a_user_has_profile ()
	{
		$this->get('profile/' . $this->user->name)
			->assertSee($this->user->name);
	}

	/** @test */
	public function profile_display_all_threads_created_by_the_associated_user ()
	{
		$this->signIn(); 

		$thread = create('App\Thread', ['user_id' => auth()->id() ]);

		$this->get('profile/' . auth()->user()->name)
			->assertSee($thread->title)
			->assertSee($thread->body);
	}
	
} 
