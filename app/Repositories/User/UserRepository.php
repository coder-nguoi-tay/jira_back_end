<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Base;

class UserRepository extends Base
{
    public function model()
    {
        return User::class;
    }
}
