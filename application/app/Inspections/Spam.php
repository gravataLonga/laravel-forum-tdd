<?php

namespace App\Inspections;

use App\Inspections\Detections\InvalidKeywords;
use App\Inspections\Detections\KeyHeldDown;

class Spam {

	protected $inspections = [
		InvalidKeywords::class,
		KeyHeldDown::class
	];

	public function detect($body)
	{
		foreach ($this->inspections as $inspection) {
			(new $inspection)->detect($body);
		}
		return false;
	}
}
