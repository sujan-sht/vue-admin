<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Contracts\Admin\UserRepositoryInterface;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    protected $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->authorizeResource(User::class, 'user');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->userRepositoryInterface->indexUser();
        return Inertia::render('admin/User/Index', [
            'users' => UserResource::collection($users)->resolve(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/User/Form', [
            'roles' => $this->userRepositoryInterface->createUser()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        // dd($request->all());
        $this->userRepositoryInterface->storeUser($request);
        return to_route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return Inertia::render('admin/User/Form', [
            'roles' => $this->userRepositoryInterface->editUser($user),
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $this->userRepositoryInterface->updateUser($request, $user);
        return to_route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->userRepositoryInterface->destroyUser($user);
        return to_route('users.index');
    }
}
