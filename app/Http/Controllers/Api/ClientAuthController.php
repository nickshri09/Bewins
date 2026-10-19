<?php

// app/Http/Controllers/Api/ClientAuthController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Member;
use App\Models\PasswordResetOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class ClientAuthController extends Controller
{
    /**
     * Get bearer token from request using multiple methods
     */
    protected function getBearerToken(Request $request)
    {
        // Try standard bearerToken() method
        $token = $request->bearerToken();

        // If bearerToken() doesn't work, try reading from header directly
        if (!$token) {
            $authHeader = $request->header('Authorization');
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
        }

        // Also try from HTTP_AUTHORIZATION server variable (for some servers)
        if (!$token) {
            $authHeader = $request->server('HTTP_AUTHORIZATION');
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
        }

        // Try from REDIRECT_HTTP_AUTHORIZATION (for some Apache configs)
        if (!$token) {
            $authHeader = $request->server('REDIRECT_HTTP_AUTHORIZATION');
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
        }

        return $token;
    }

// public function register(Request $request)
// {

//     $validated = $request->validate([
//         'first_name' => 'required|string|max:100',
//         'last_name'  => 'nullable|string|max:100',
//         // 'member_no'  => 'required|string|unique:members,member_no',
//         'email'      => 'required|email|unique:members,email',
//         'country_code' => 'nullable|string|max:10',
//         'mobile' => 'nullable|string|max:30',
//         'password'   => 'required|string|min:8',
//     ]);
//     // dd($validated);
//     $member = Member::create([
//         'first_name'    => $validated['first_name'],
//         'last_name'     => $validated['last_name'] ?? null,
//         // 'member_no'     => $validated['member_no'],
//         'email'         => $validated['email'],
//         'country_code'  => $validated['country_code'] ?? null,
//         'mobile'        => $validated['mobile'] ?? null,
//         'password'      => Hash::make($validated['password']),
//     ]);

//     $token = $member->createToken('member-token')->plainTextToken;

//     return response()->json([
//         'status' => 'success',
//         'message' => 'Member registered successfully.',
//         'data' => [
//             'member' => $member,
//             'token'  => $token,
//         ]
//     ], 201);
// }

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name'   => 'required|string|max:100',
        'last_name'    => 'nullable|string|max:100',
        'email'        => 'required|email|unique:members,email',
        'country_code' => 'nullable|string|max:10',
        'mobile'       => 'nullable|string|max:30',
        'password'     => 'required|string|min:8',
    ]);

    //Validation Fail Response
    if ($validator->fails()) {
        return response()->json([
            'status'  => 'failed',
            'message' => $validator->errors()->first(),
            // 'errors'  => $validator->errors()
        ], 422);
    }

    $validated = $validator->validated();

    $member = Member::create([
        'first_name'   => $validated['first_name'],
        'last_name'    => $validated['last_name'] ?? null,
        'email'        => $validated['email'],
        'country_code' => $validated['country_code'] ?? null,
        'mobile'       => $validated['mobile'] ?? null,
        'password'     => Hash::make($validated['password']),
    ]);

    $token = $member->createToken('member-token')->plainTextToken;

    return response()->json([
        'status'  => 'success',
        'message' => 'Member registered successfully.',
        'data'    => [
            'member' => $member,
            'token'  => $token,
        ]
    ], 201);
}
//   public function login(Request $request)
// {
//     $request->validate([
//         'email' => 'nullable|email',
//         'country_code' => 'nullable|string',
//         'mobile' => 'nullable|string',
//         'password' => 'required|string|min:6', // match register rule
//     ]);

//     // Allow login by email OR by mobile + country_code
//     $member = null;

//     if (!empty($request->email)) {
//         $member = Member::where('email', $request->email)->first();
//     } elseif (!empty($request->mobile)) {
//         $member = Member::where('country_code', $request->country_code)
//                         ->where('mobile', $request->mobile)
//                         ->first();
//     }

//     if (! $member || ! Hash::check($request->password, $member->password)) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Invalid credentials. Please check your email or mobile and password.',
//         ], 401);
//     }

//     // Optional: Revoke old tokens for single-device login
//     // $member->tokens()->delete();

//     $token = $member->createToken('member-token')->plainTextToken;

//     return response()->json([
//         'status' => 'success',
//         'message' => 'Login successful.',
//         'data' => [
//             'member' => $member,
//             'token' => $token,
//         ]
//     ], 200);
// }



public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email'         => 'nullable|email',
        'country_code'  => 'nullable|string',
        'mobile'        => 'nullable|string',
        'password'      => 'required|string|min:6',
    ]);

    //  Validation failed response
    if ($validator->fails()) {
        return response()->json([
            'status'  => 'failed',
            'message' => $validator->errors()->first(),
        ], 422);
    }

    // Allow login by email OR by mobile + country_code
    $member = null;

    if (!empty($request->email)) {
        $member = Member::where('email', $request->email)->first();
    } elseif (!empty($request->mobile)) {
        $member = Member::where('country_code', $request->country_code)
                        ->where('mobile', $request->mobile)
                        ->first();
    }

    // Invalid credentials response as failed
    if (!$member || !Hash::check($request->password, $member->password)) {
        return response()->json([
            'status'  => 'failed',
            'message' => 'Invalid credentials.',
        ], 400);
    }

    $token = $member->createToken('member-token')->plainTextToken;

    return response()->json([
        'status'  => 'success',
        'message' => 'Login successful.',
        'data' => [
            'member' => $member,
            'token'  => $token,
        ]
    ], 200);
}


    public function logout(Request $request)
    {
        try {
            $token = $this->getBearerToken($request);

            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. No token provided.',
                    'debug' => [
                        'bearerToken' => $request->bearerToken() ? 'exists' : 'null',
                        'auth_header' => $request->header('Authorization') ? 'exists' : 'null',
                        'http_auth' => $request->server('HTTP_AUTHORIZATION') ? 'exists' : 'null',
                        'redirect_auth' => $request->server('REDIRECT_HTTP_AUTHORIZATION') ? 'exists' : 'null'
                    ]
                ], 401);
            }

            // Find and delete the token directly
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. Invalid token.',
                    'debug' => [
                        'token_preview' => substr($token, 0, 20) . '...'
                    ]
                ], 401);
            }

            // Delete the token
            $accessToken->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }




    public function Profile_update(Request $request)
    {
        try {
            $token = $this->getBearerToken($request);
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. No token provided.'
                ], 401);
            }

            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if (!$accessToken || !$accessToken->tokenable_type) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. Invalid token.'
                ], 401);
            }

            // Get member (without global scopes)
            $client = $accessToken->tokenable_type::withoutGlobalScopes()
                ->find($accessToken->tokenable_id);

            if (!$client) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Member not found.'
                ], 404);
            }

            //  Validation as per DB columns
            $validated = $request->validate([
                'business_name'  => 'nullable|string|max:255',
                'branch_id'      => 'nullable|integer|exists:branches,id',
                'gender'         => 'nullable|in:male,female,other',
                'city'           => 'nullable|string|max:100',
                'state'          => 'nullable|string|max:100',
                'zip'            => 'nullable|string|max:20',
                'credit_source'  => 'nullable|string|max:255',
                'address'        => 'nullable|string',
                'photo'          => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
                'pancard_no'     => 'nullable|string|max:50',
                'pancard_image'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'aadhaar_no'     => 'nullable|string|max:50',
                'aadhaar_front'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'aadhaar_back'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            //  Handle file uploads
            $paths = [];
            $uploadFields = ['photo', 'pancard_image', 'aadhaar_front', 'aadhaar_back'];

            foreach ($uploadFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('public/members', $filename);
                    $paths[$field] = 'members/' . $filename;
                }
            }

            //  Update record
            $client->update(array_merge($validated, [
                'photo'          => $paths['photo'] ?? $client->photo,
                'pancard_image'  => $paths['pancard_image'] ?? $client->pancard_image,
                'aadhaar_front'  => $paths['aadhaar_front'] ?? $client->aadhaar_front,
                'aadhaar_back'   => $paths['aadhaar_back'] ?? $client->aadhaar_back,
            ]));

            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully.',
                'data'    => $client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profile update failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
 *  Get All Branches List (for Client App)
 */
public function getBranches()
{
    try {
        $branches = \App\Models\Branch::select('id', 'name', 'contact_email', 'contact_phone', 'address')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Branch list fetched successfully.',
            'data'    => $branches
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to fetch branches.',
            'error'   => $e->getMessage()
        ], 500);
    }
}



 public function profile_show(Request $request)
{
    try {
        $token = $this->getBearerToken($request);

        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated. No token provided.'
            ], 401);
        }

        // 🔹 Find token and get the authenticated member
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
       // dd($accessToken);
        // if (!$accessToken || !$accessToken->tokenable) {
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => 'Unauthenticated. Invalid token.'
        //     ], 401);
        // }

        // $client = $accessToken->tokenable->load('branch');


        if (!$accessToken || !$accessToken->tokenable_type) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Invalid token.'
            ], 401);
        }

        // Get member (without global scopes)
        $client = $accessToken->tokenable_type::withoutGlobalScopes()
            ->find($accessToken->tokenable_id)->load('branch');

        // 🔹 Prepare structured response
        $data = [
            'id'             => $client->id,
            'first_name'     => $client->first_name,
            'last_name'      => $client->last_name,
            'email'          => $client->email,
            'mobile'         => $client->mobile,
            'gender'         => $client->gender,
            'city'           => $client->city,
            'state'          => $client->state,
            'zip'            => $client->zip,
            'address'        => $client->address,
            'credit_source'  => $client->credit_source,
            'business_name'  => $client->business_name,
            'photo'          => $client->photo ? asset('public/uploads/members/'.$client->photo) : null,
            'pan_number'     => $client->pan_number,
            'pan_image'      => $client->pan_image ? asset('public/uploads/members/'.$client->pan_image) : null,
            'aadhaar_number' => $client->aadhaar_number,
            'aadhaar_front'  => $client->aadhaar_front ? asset('public/uploads/members/'.$client->aadhaar_front) : null,
            'aadhaar_back'   => $client->aadhaar_back ? asset('public/uploads/members/'.$client->aadhaar_back) : null,
            'branch' => $client->branch ? [
                'id'            => $client->branch->id,
                'name'          => $client->branch->name,
                'contact_email' => $client->branch->contact_email,
                'contact_phone' => $client->branch->contact_phone,
                'address'       => $client->branch->address,
            ] : null,
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile fetched successfully.',
            'data'    => $data
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to fetch profile.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Forgot Password - Send OTP to user email
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = $request->email;
        
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Delete any existing OTPs for this email
        PasswordResetOtp::where('email', $email)->delete();
        
        // Create new OTP record
        $passwordResetOtp = PasswordResetOtp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(30), // OTP expires in 30 minutes
            'verified' => false,
        ]);

        try {
            // Send OTP to email (you may need to configure email settings)
            Mail::raw("Your password reset OTP is: {$otp}\n\nThis OTP will expire in 30 minutes.", function ($message) use ($email) {
                $message->to($email)
                    ->subject('Password Reset OTP');
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'OTP sent to your email successfully.',
                'data'    => [
                    'email' => $email,
                    'otp' => $otp, // For testing only - remove in production
                    'otp_expires_at' => $passwordResetOtp->expires_at,
                    'debug' => $passwordResetOtp->expires_at?->format('d-m-Y h:i A'),

                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to send OTP. Please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $passwordResetOtp = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$passwordResetOtp) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Invalid OTP.',
            ], 400);
        }

        if ($passwordResetOtp->isExpired()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'OTP has expired. Please request a new one.',
                'debug' => [    
                    'current_time' => now()->format('d-m-Y h:i A'),
                    'expires_at' => $passwordResetOtp->expires_at?->format('d-m-Y h:i A'),
                    'is_expired' => $passwordResetOtp->isExpired()
                ]
            ], 400);
        }

        if ($passwordResetOtp->verified) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'OTP has already been used.',
            ], 400);
        }

        // Mark OTP as verified
        $passwordResetOtp->verified = true;
        $passwordResetOtp->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'OTP verified successfully. You can now reset your password.',
            'data'    => [
                'email' => $request->email,
                'verified' => true,
            ],
            'debug' => [
                'current_time' => now()->format('d-m-Y h:i A'),
                'expires_at' => $passwordResetOtp->expires_at?->format('d-m-Y h:i A'),
                'is_expired' => $passwordResetOtp->isExpired()
            ]
        ], 200);
    }

    /**
     * Reset Password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email',
            'otp'      => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Verify OTP exists and is verified
        $passwordResetOtp = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('verified', true)
            ->first();

        if (!$passwordResetOtp) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Invalid or unverified OTP. Please verify OTP first.',
            ], 400);
        }

        if ($passwordResetOtp->isExpired()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'OTP has expired. Please request a new one.',
                'debug' => [
                    'current_time' => now()->format('d-m-Y h:i A'),
                    'expires_at' => $passwordResetOtp->expires_at?->format('d-m-Y h:i A'),
                    'is_expired' => $passwordResetOtp->isExpired()
                ]
            ], 400);
        }

        // Find and update user password
        $user = \App\Models\Member::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'User not found.',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the used OTP
        $passwordResetOtp->delete();

        // Optional: Revoke all existing tokens for security
        $user->tokens()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Password reset successfully. Please login with your new password.',
            'data'    => [
                'email' => $request->email,
            ]
        ], 200);
    }






}

