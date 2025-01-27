<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Models\User;
use App\Traits\PadsArrayWithNull;
use Arr;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserUpdateController extends Controller
{
    use PadsArrayWithNull;

    public function __invoke(User $user, UserUpdateRequest $request): JsonResponse
    {
        // Password is not always required because the user doesn't get it
        // Flip twice because Arr::except works with keys, not values
        $userFillable = array_flip(Arr::except(array_flip((new User)->getFillable()), ['password']));

        $user->update($this->padArrayWithNull($userFillable, $request->validated()));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
