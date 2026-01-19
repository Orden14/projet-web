<?php

namespace App\Tests\Acceptance\Security;

use App\Tests\Support\AcceptanceTester;

final readonly class LoginCest
{
    public function testLoginWithValidCredentials(AcceptanceTester $I): void
    {
        $I->loginAs('admin');
        $I->seeCurrentUrlEquals('/');
    }

    public function testLoginWithInvalidCredentials(AcceptanceTester $I): void
    {
        $I->loginAs('invalidAccount');
        $I->seeCurrentUrlEquals('/login');
        $I->seeElement('#login-error-flash');
    }
}
