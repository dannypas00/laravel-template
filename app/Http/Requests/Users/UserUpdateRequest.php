<?php

namespace App\Http\Requests\Users;

use Auth;

class UserUpdateRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return Auth::id() == $this->route('user')?->id;
    }
}
