<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show()
    {
        return UserResource::make(auth()->user())
            ->response()
            ->setStatusCode(config('httpstatus.success'));
    }

    /**
     * @param UserUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request)
    {
        /** @var User $auth */
        $auth = $request->user();

        $user = $this->userRepository->update($auth, $request->only('email', 'name', 'password'));

        return UserResource::make($user)
            ->response()
            ->setStatusCode(config('httpstatus.updated'));
    }
}
