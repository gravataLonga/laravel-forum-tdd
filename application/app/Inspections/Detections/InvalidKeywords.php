<?php

namespace App\Inspections\Detections;

use Exception;
use App\Inspections\Exception\SpamException;
use App\Inspections\Contract\DetectionInterface;

class InvalidKeywords implements DetectionInterface
{
	protected $keywords = [
		'yahoo customer support',
		'fuck',
	];

	public function detect($body)
	{
		foreach ($this->keywords as $word) {
			if (stripos($body, $word) !== false) {
				throw new SpamException("Spam was detected", 1);
			}
		}
	}
}