<?php

declare(strict_types=1);

namespace Manager\Application\User\Creator;

use Manager\Domain\User\Exceptions\CreateUserException;
use Manager\Domain\User\Exceptions\UserAlreadyExistsException;
use Manager\Domain\User\User;
use Manager\Domain\User\UserRepository;
use Manager\Domain\User\ValueObjects\UserEmail;
use Manager\Domain\User\ValueObjects\UserName;
use Manager\Domain\User\ValueObjects\UserPassword;
use Manager\Shared\Domain\Bus\Event\EventBus;

final readonly class UserCreator
{
	public function __construct(private UserRepository $repository, private EventBus $bus) {}

	public function __invoke(UserName $name, UserEmail $email, UserPassword $password): void
	{
		$user = $this->repository->searchByEmail($email);

		if ($user !== null) {
			throw new UserAlreadyExistsException($user->id());
		}

		$user = User::create(id: null, name: $name, email: $email, password: $password);
		$userCreated = $this->repository->create($user);
		if (!$userCreated) {
			throw new CreateUserException($user->email());
		}

        $this->bus->publish(...$user->pullDomainEvents());
	}
}
