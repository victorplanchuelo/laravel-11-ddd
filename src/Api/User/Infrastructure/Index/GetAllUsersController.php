<?php

declare(strict_types=1);

namespace Manager\Api\User\Infrastructure\Index;

use Illuminate\Http\JsonResponse;
use Manager\Api\User\Application\Searcher\GetAllUsersQuery;
use Manager\Api\User\Application\UserResponse;
use Manager\Shared\ApiBaseController;
use Manager\Shared\Domain\Bus\Query\QueryBus;
use function Lambdish\Phunctional\map;

final class GetAllUsersController extends ApiBaseController
{
	public function __construct(protected readonly QueryBus $bus) {}

	public function __invoke(): JsonResponse
	{
		$response = $this->bus->ask(new GetAllUsersQuery());

		return new JsonResponse(
			map(
				fn (UserResponse $user): array => [
					'uuid' => $user->uuid(),
					'id' => $user->id(),
					'name' => $user->name(),
					'email' => $user->email(),
				],
				$response->users()
			),
			200,
			['Access-Control-Allow-Origin' => '*']
		);
	}
}
