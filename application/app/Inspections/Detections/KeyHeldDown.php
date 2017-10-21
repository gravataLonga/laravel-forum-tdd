<?php

namespace App\Inspections\Detections;

use App\Inspections\Contract\DetectionInterface;
use Exception;

class KeyHeldDown implements DetectionInterface
{
	public function detect($body)
	{
		if (preg_match('/(.)\\1{4,}/', $body, $matches)) {
			throw new \Exception("Error Processing Request", 1);
		}
	}
}