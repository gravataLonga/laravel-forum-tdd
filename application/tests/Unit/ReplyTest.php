<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function it_has_owner_property()
	{
		$reply = create('App\Reply');

		$this->assertInstanceOf('App\User', $reply->owner);
	}
}
