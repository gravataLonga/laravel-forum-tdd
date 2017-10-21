<?php

namespace App\Inspections\Detections;

use App\Inspections\Contract\DetectionInterface;
use Exception;

class InvalidKeywords implements DetectionInterface
{

	protected $keywords = [
		'yahoo customer support',
		'fuck',
		'ass'
	];

	public function detect($body)
	{
		foreach ($this->keywords as $word) {
			if (stripos($body, $word) !== false) {
				throw new Exception("Error Processing Request", 1);
			}
		}
	}
}