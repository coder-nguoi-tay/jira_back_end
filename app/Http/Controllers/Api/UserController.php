<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * dang nhap
     * @param \App\Http\Requests\LoginRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(LoginRequest $request)
    {
        $credentials = $request->validated(); // <--- SỬ DỤNG validated()

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Đăng nhập thành công!',
                'user' => $user->only('id', 'name', 'email'),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Thông tin đăng nhập không hợp lệ.',
            ], 401);
        }
    }

    /**
     * Xử lý yêu cầu đăng ký người dùng mới.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        // Dữ liệu đã được xác thực bởi RegisterRequest
        $validatedData = $request->validated();

        try {
            // Tạo người dùng mới
            $user = $this->userRepository->create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Tùy chọn: Đăng nhập người dùng ngay sau khi đăng ký
            Auth::login($user);

            // Tạo API token cho người dùng mới (nếu bạn dùng Laravel Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Đăng ký tài khoản thành công!',
                'user' => $user->only('id', 'name', 'email'),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201); // HTTP 201 Created

        } catch (\Exception $e) {
            // Xử lý các lỗi có thể xảy ra trong quá trình tạo người dùng
            return response()->json([
                'message' => 'Đăng ký tài khoản thất bại.',
                'error' => $e->getMessage() // Chỉ hiển thị lỗi trong môi trường dev/debug
            ], 500); // HTTP 500 Internal Server Error
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
