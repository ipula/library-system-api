<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTO\PatchUserDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\User\Repositories\UserRepository;

class UpdateUser
{
    public function __construct(
        private UserRepository $repository
    ) {}

    /**
     * @param PatchUserDTO $userDTO
     * @return UserDTO|null
     */
    public function execute(PatchUserDTO $userDTO): ?UserDTO
    {
        $user = $this->repository->findById($userDTO->id);
        if (!$user) {
            return null;
        }

        $data = $userDTO->data;

        $map = [
            'name'  => 'setName',
            'email' => 'setEmail',
            'password'   => 'setPassword',
        ];

        foreach ($map as $field => $setter) {
            if (array_key_exists($field, $data)) {
                $user->{$setter}($data[$field]);
            }
        }
        $this->repository->save($user);
        return UserDTO::fromEntity($user);
    }
}
