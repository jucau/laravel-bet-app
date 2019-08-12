<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Client;

class RegisterController extends Controller
{
    protected $userRepository;

    /**
     * RegisterController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    function store(Request $request)
    {
        $validation = validator($request->only('username', 'password', 'client_id', 'client_secret'), [
            'username' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'client_id' => [
                'required',
                Rule::exists('oauth_clients', 'id'),
            ],
            'client_secret' => [
                'required',
                Rule::exists('oauth_clients', 'secret')
                    ->where('id', request()->get('client_id', 0))
            ]
        ]);

        if ($validation->fails()) {
            throw (new ValidationException($validation))
                ->errorBag($validation->errors()->all());
        }

        $this->userRepository->create([
            'email'      => $request->input('username'),
            'password'      => $request->input('password'),
        ]);

        $client = Client::where('password_client', 1)->first();

        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $request->input('username'),
            'password'      => $request->input('password'),
        ]);

        $proxy = Request::create(route('passport.token', [], false), 'POST');

        return \Route::dispatch($proxy);
    }
}
