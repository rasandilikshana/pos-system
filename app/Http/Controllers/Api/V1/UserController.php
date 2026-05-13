<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Users\SaveUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request, UserRepository $users): AnonymousResourceCollection
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return UserResource::collection(
            $users->paginateFiltered((string) $request->string('q'), $request->integer('per_page', 25))
        );
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('roles'));
    }

    public function store(StoreUserRequest $request, SaveUserAction $save): JsonResponse
    {
        $user = $save->execute($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, User $user, SaveUserAction $save): UserResource
    {
        return new UserResource($save->execute($request->validated(), $user->id));
    }

    public function destroy(Request $request, User $user, UserRepository $users): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => __('You cannot delete your own account.')], 422);
        }

        $users->delete($user->id);

        return response()->json(null, 204);
    }
}
