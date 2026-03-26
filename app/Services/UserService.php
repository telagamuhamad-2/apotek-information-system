<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new user
     */
    public function create(array $data)
    {
        // Hash the password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = parent::create($data);

        // Assign role if provided
        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    /**
     * Update an existing user
     */
    public function update(int $id, array $data)
    {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = parent::update($id, $data);

        // Update role if provided
        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * Find users by role
     */
    public function findByRole(string $role)
    {
        return $this->repository->findByRole($role);
    }

    /**
     * Get all employees (users with 'pegawai' role)
     */
    public function getEmployees()
    {
        return $this->repository->all(['role' => 'pegawai']);
    }
}
