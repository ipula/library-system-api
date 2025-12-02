<?php

namespace App\Interfaces\Http\Controllers\User;

use App\Application\User\DTO\PatchUserDTO;
use App\Application\User\DTO\RegisterUserInput;
use App\Application\User\UseCases\DeleteUser;
use App\Application\User\UseCases\FetchAllUser;
use App\Application\User\UseCases\GetUserById;
use App\Application\User\UseCases\RegisterUser;
use App\Application\User\UseCases\UpdateUser;
use App\Interfaces\Http\Controllers\Controller;
use App\Interfaces\Http\Requests\User\RegisterUserRequest;
use App\Interfaces\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User management"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private RegisterUser $registerUser,
        private FetchAllUser $fetchUser,
        private DeleteUser   $deleteUser,
        private UpdateUser   $updateUser,
        private GetUserById  $getUserById,
    )
    {

    }

    /**
     * @OA\Get(
     *     path="/v1/users",
     *     tags={"Users"},
     *     summary="List users",
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $users = $this->fetchUser->getAll($request);
        return response()->json([
            'data' => $users->toArray()
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Get a single user",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(int $id)
    {
        $user = $this->getUserById->execute($id);
        if (!$user) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'data' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/users",
     *     tags={"Users"},
     *     summary="Create a user",
     *     description="Public endpoint to create a user account.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8)
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(RegisterUserRequest $request)
    {
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
    }

    /**
     * @OA\Patch(
     *     path="/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $dto = new PatchUserDTO(
            id: $id,
            data: $request->validated()
        );
        $user = $this->updateUser->execute($dto);
        if (!$user) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'data' => $user,
            'message' => 'user updated successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/v1/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User deleted"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(int $id)
    {
        $user = $this->deleteUser->execute($id);
        if (!$user) {
            return response()->json(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'user deleted successfully'
        ], Response::HTTP_CREATED);
    }
}
