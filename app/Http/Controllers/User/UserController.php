<?php

namespace App\Http\Controllers\User;

use App\Application\User\DTO\RegisterUserInput;
use App\Application\User\UseCases\RegisterUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(private RegisterUser $registerUser){

    }

    public function index(Request $request){
        return response()->json(['asdasdasd'], Response::HTTP_OK);
    }

    public function show(Request $request){}
    public function store(RegisterUserRequest $request){
        try {
            $input = new RegisterUserInput(
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password'),
            );

            $userDTO = $this->registerUser->execute($input);

            return response()->json([
                'data' => $userDTO->toArray(),
                'message' => 'user registered successfully'
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function update(Request $request){}
    public function destroy(Request $request){}
}
