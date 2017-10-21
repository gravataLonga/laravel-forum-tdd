<?php

namespace App\Inspections\Contract;

interface DetectionInterface
{
	public function detect($body);
}