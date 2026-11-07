<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\CartService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthenticationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private CartService $cartService
    ) {}

    public function mergeCart(Request $request, User $user): array
    {
        $token = $request->header('X-Cart-Token') ?? null;
        $cart = $this->cartService->findOrCreateForVisitor($token);
        return $this->cartService->mergeVisitorIntoUser($cart, $user);
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register(
            $request->validated(),
            $request
        );
        // independente do token que for mandado sempre cria um novo
        $response = $this->mergeCart($request, $user);
        return (new UserResource($user))
            ->additional(['warnings' => $response['warnings']])
            ->response()
            ->setStatusCode(201)
            ->header('Cache-Control', 'no-store')
            ->header('X-Cart-Token', $response['cart']->token);;
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login(
            $request->validated(),
            $request
        );
        $token = $request->header('X-Cart-Token') ?? null;
        $currentCart = $this->cartService->getByToken($token);
        $userCart = $this->cartService->getByUser($user);
        $response = null;
        if (!$currentCart) {
            $response = $this->mergeCart($request, $user);
        }
        elseif ($currentCart->user_id === null) {
            $response = $this->mergeCart($request, $user);
        }
        elseif ($currentCart->user_id !== $user->id || $userCart->token !== $token) {
            throw ValidationException::withMessages([
                'message' => 'Carrinho de outro usuário.'
            ]);
        }
        return (new UserResource($user))
            ->additional(['warnings' => $response['warnings']])
            ->response()
            ->header('Cache-Control', 'no-store')
            ->header('X-Cart-Token', (!$response) ? $userCart->token : $response['cart']->token);
    }

    public function me(Request $request)
    {
        if (!$request->user()) {
            throw new AuthenticationException('The user is not logged in.');
        }
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return response()->noContent();
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $this->authService->update($request->validated(), $request);

        return new UserResource($user);
    }
}