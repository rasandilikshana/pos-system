<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaveUserAction
{
    public function __construct(private readonly UserRepository $users) {}

    /**
     * @param  array{name:string,email:string,role:string,is_active:bool,password?:?string}  $attrs
     */
    public function execute(array $attrs, ?int $id = null): User
    {
        return DB::transaction(function () use ($attrs, $id) {
            $role = $attrs['role'];
            $password = $attrs['password'] ?? null;
            unset($attrs['role'], $attrs['password']);

            if ($password) {
                $attrs['password'] = Hash::make($password);
            }

            if ($id) {
                $user = $this->users->update($id, $attrs);
            } else {
                $attrs['email_verified_at'] = now();
                $user = $this->users->create($attrs);
            }

            $user->syncRoles([$role]);

            return $user->fresh(['roles']);
        });
    }
}
