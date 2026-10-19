<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Notifications\TwoFactorCode;
use App\Providers\RouteServiceProvider;
use App\Utilities\Overrider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
     * |--------------------------------------------------------------------------
     * | Login Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller handles authenticating users for the application and
     * | redirecting them to your home screen. The controller uses a trait
     * | to conveniently provide its functionality to your applications.
     * |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->role && strtolower($user->role->name) == 'employee') {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', date('Y-m-d'))
                ->first();
            if ($attendance) {
                $attendance->logout_time = date('H:i:s');
                $attendance->save();
            }
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/');
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => $request->{$this->username()},
            'password' => $request->password,
            'status' => 1,
        ];
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        config(['recaptchav3.sitekey' => get_option('recaptcha_site_key')]);
        config(['recaptchav3.secret' => get_option('recaptcha_secret_key')]);

        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'g-recaptcha-response' => get_option('enable_recaptcha', 0) == 1 ? 'required|recaptchav3:login,0.5' : '',
        ], [
            'g-recaptcha-response.recaptchav3' => _lang('Recaptcha error!'),
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->status != 1) {
            $errors = [$this->username() => _lang('Your account is not active !')];
            Auth::logout();
            return back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($errors);
        }

        if (get_option('email_2fa_status', 0) == 1) {
            Overrider::load('Settings');
            date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
            $user->resetTwoFactorCode();
            $user->generateTwoFactorCode();
            try {
                $user->notify(new TwoFactorCode());
            } catch (\Exception $e) {
                return back()->with('error', 'SMTP Configuration is incorrect !');
            }
        }

        // Attendance Recording for Employees
        if ($user->role && strtolower($user->role->name) == 'employee') {
            $attendance = Attendance::firstOrCreate([
                'user_id' => $user->id,
                'date' => date('Y-m-d'),
            ]);

            if ($attendance->login_time == null) {
                $attendance->login_time = date('H:i:s');
                $attendance->save();
            }
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        $user = \App\Models\User::where($this->username(), $request->{$this->username()})->first();

        if ($user && Hash::check($request->password, $user->password) && $user->status != 1) {
            $errors = [$this->username() => _lang('Your account is not active !')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * Send OTP to user email
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'No account found with this email address.'
        ]);

        $email = $request->email;
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.'
            ], 404);
        }

        if ($user->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active.'
            ], 403);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database with expiration
        DB::table('login_otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );

        // Send OTP via email
        try {
            // Load mail settings
            Overrider::load('Settings');

            $this->sendOtpEmail($email, $otp, $user->name);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email.'
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('OTP Send Error: ' . $e->getMessage());

            // For development/testing, return OTP in response if mail fails
            if (app()->environment('local') || app()->environment('development')) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP generated successfully. (Mail not configured - OTP: ' . $otp . ')',
                    'debug_otp' => $otp
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Mail configuration error. Please contact administrator.'
            ], 500);
        }
    }

    /**
     * Login with OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginWithOtp(Request $request)
    {
        $request->validate([
            'otp_email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6'
        ], [
            'otp_email.exists' => 'No account found with this email address.',
            'otp_code.size' => 'OTP must be 6 digits.'
        ]);

        $email = $request->otp_email;
        $otp = $request->otp_code;

        // Check if OTP exists and is valid
        $otpRecord = DB::table('login_otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors([
                'otp_code' => 'Invalid or expired OTP. Please try again.'
            ])->withInput($request->only('otp_email'));
        }

        // Get user
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user || $user->status != 1) {
            return back()->withErrors([
                'otp_email' => 'Account not found or inactive.'
            ]);
        }

        // Login user
        Auth::login($user, $request->has('remember'));

        // Delete used OTP
        DB::table('login_otps')->where('email', $email)->delete();

        // Handle 2FA if enabled
        if (get_option('email_2fa_status', 0) == 1) {
            Overrider::load('Settings');
            date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
            $user->resetTwoFactorCode();
            $user->generateTwoFactorCode();
            try {
                $user->notify(new TwoFactorCode());
            } catch (\Exception $e) {
                return back()->with('error', 'SMTP Configuration is incorrect !');
            }
        }

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Send OTP email
     *
     * @param string $email
     * @param string $otp
     * @param string $name
     * @return void
     */
    private function sendOtpEmail($email, $otp, $name)
    {
        $subject = 'Login OTP - ' . get_option('company_name');

        $message = "
        <html>
        <head>
            <title>Login OTP</title>
        </head>
        <body>
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #1a237e; color: white; padding: 20px; text-align: center;'>
                    <h2>" . get_option('company_name') . "</h2>
                </div>
                <div style='padding: 30px; background: #f9f9f9;'>
                    <h3>Hello " . $name . ",</h3>
                    <p>Your login OTP is:</p>
                    <div style='background: #1a237e; color: white; padding: 20px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; margin: 20px 0;'>
                        " . $otp . "
                    </div>
                    <p><strong>This OTP will expire in 10 minutes.</strong></p>
                    <p>If you didn't request this OTP, please ignore this email.</p>
                    <p>Best regards,<br>" . get_option('company_name') . "</p>
                </div>
                <div style='background: #333; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                    <p>This is an automated email. Please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Get mail settings
        $fromEmail = get_option('from_email', 'noreply@example.com');
        $fromName = get_option('from_name', get_option('company_name', 'System'));

        // Send email using Laravel's mail system
        Mail::html($message, function ($message) use ($email, $subject, $fromEmail, $fromName) {
            $message
                ->to($email)
                ->subject($subject)
                ->from($fromEmail, $fromName);
        });
    }
}
