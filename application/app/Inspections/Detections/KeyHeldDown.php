<?php

namespace App\Inspections\Detections;

use Exception;
use App\Inspections\Exception\SpamException;
use App\Inspections\Contract\DetectionInterface;

class KeyHeldDown implements DetectionInterface
{
	public function detect($body)
	{
		if (preg_match('/(.)\\1{4,}/', $body, $matches)) {
			throw new SpamException("Spam was detected", 1);
		}
	}
}