<?php

declare(strict_types=1);

namespace Tests\Shared\Domain;

final class UuidMother
{
	public static function create(): string
	{
		return MotherCreator::random()->unique()->uuid;
	}
}
