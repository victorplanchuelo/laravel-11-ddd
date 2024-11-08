<?php

declare(strict_types=1);

namespace Tests\Api\User\Infrastructure;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Api\User\Domain\ObjectMother\UserEmailMother;
use Tests\Api\User\Domain\ObjectMother\UserIdMother;
use Tests\Api\User\Domain\ObjectMother\UserMother;
use Tests\Api\User\Domain\UserCriteriaMother;
use Tests\Shared\Domain\Criteria\CriteriaMother;

final class MySqlUserRepositoryTest extends UserInfrastructureTestCase
{
	use DatabaseMigrations;

	public function test_it_should_list_all_users(): void
	{
		$user = UserMother::create(id: UserIdMother::create(1));
		$anotherUser = UserMother::create(id: UserIdMother::create(2));
		$existingUsers = [$user, $anotherUser];

		$this->mySqlRepository()->save($user);
		$this->mySqlRepository()->save($anotherUser);

		//$this->assertEquals($existingUsers, $this->mySqlRepository()->searchAll());
		$this->assertSimilar($existingUsers, $this->mySqlRepository()->searchAll());
	}

	public function test_it_should_create_a_user(): void
	{
		$this->mySqlRepository()->save(UserMother::create());
	}

    public function test_it_should_return_a_user_when_search_by_criteria(): void
    {
        $emailToSearch = UserEmailMother::create('test@test.com');

        $user = UserMother::create(id: UserIdMother::create(1), email: $emailToSearch);
        $this->mySqlRepository()->save($user);

        $criteria = UserCriteriaMother::emailEqualsTo($emailToSearch->value());

        $this->assertSimilar([$user], $this->mySqlRepository()->matching($criteria));
    }
}
