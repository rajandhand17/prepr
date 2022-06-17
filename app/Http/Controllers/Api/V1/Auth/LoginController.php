<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(){
		$this->middleware('guest');
	}

	/**
	 *
	 * @OA\Post(
	 *     tags={"Authentication"},
	 *     path="/api/login",
	 *     operationId="login",
	 *     summary="User login API",
	 *     description="User login",
	 *     @OA\Header(
	 *         header="Content-Type",
	 *         @OA\Schema(
	 *             type="object",
	 *              @OA\Property(property="Content-Type", type="string", default="application/json"),
	 *              @OA\Property(property="Accept", type="string", default="application/json"),
	 *         ),
	 *     ),
	 *     @OA\RequestBody(
	 *          @OA\MediaType(
	 *              mediaType="application/x-www-form-urlencoded",
	 *              @OA\Schema(
	 *                  type="object",
	 *                  @OA\Property(property="email", type="string", example="admin@gmail.com"),
	 *                  @OA\Property(property="password", type="string", example="mbd@2021"),
	 *              ),
	 *          ),
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="User Authorized",
	 *          @OA\JsonContent(
	 *              @OA\Property(property="status", type="boolean", example="true"),
	 *              @OA\Property(property="data", type="object",
	 *                  @OA\Property(property="email", type="string", example="-----@gmail.com"),
	 *                  @OA\Property(property="password", type="string", example="abc1234"),
	 *              ),
	 *              @OA\Property(property="message", type="array",
	 *                  @OA\Items(type="string", default="User Authorized"),
	 *              ),
	 *          ),
	 *     ),
	 *     @OA\Response(
	 *          response=400,
	 *          description="Your email is not verified!",
	 *          @OA\JsonContent(
	 *              @OA\Property(property="message", type="string", example="You have provided wrong credentials (or) your account does not exits!"),
	 *              @OA\Property(property="errors", type="object"),
	 *          ),
	 *     ),
	 * )
	 */
	public function login(Request $request){
		// Validate
		$this->validate($request, [
			'username'  => 'sometimes|string|alpha_dash|max:255|min:4',
			'email'     => 'sometimes|string|email|max:255',
			'password'  => 'required|string|min:6|max:64',
		]);

		if(!empty($request->email)){
			// Get user with email address
			$user = User::where('email', $request->email)->first();
		} else if(!empty($request->username)) {
			// Get user with username
			$user = User::where('username', $request->username)->first();
		}

		if(!empty($user) && Hash::check($request->password, $user->password)){
			if(!empty($user->email_verified_at)){              
				return response()->json(array(
				'data' => array(
					'token'     => $user->createToken('Auth Token')->accessToken
				),
				'status' => true,
				'message' => array('User Authorized')
				));
			} 
			else {
				return response()->json(array(
				'data' => array(),
				'status' => false,
				'message' => array('Your email is not verified!')
				), 400);
			} 
		}

		return response()->json(array(
			'data' => array(),
			'status' => false,
			'message' => array('You have provided wrong credentials (or) your account does not exits!')
		), 400);
	}
}
