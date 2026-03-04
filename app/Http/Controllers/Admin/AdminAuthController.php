<?php
namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Mail\AdminResetPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login() {
        return view('admin.loginPages.login');
    }

    public function index() {
        return view('admin.loginPages.dashboard');
    }
    public function doLogin(Request $request)
    {
        // Validate input fields
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            //'g-recaptcha-response' => ['sometimes', 'required'],
        ]);
        // Generate a unique key for throttling
        $throttleKey = $request->ip();
        RateLimiter::clear($throttleKey);
        // Check if too many login attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 2)) {
            session(['captcha_required' => true]);
            $this->validateCaptcha(request: $request);
        }
        // Attempt login
        $rememberMe = $request->has('rememberme');
        if (adminAuth()->attempt($request->only(['email', 'password']), $rememberMe)) {
            // Clear throttle attempts on successful login
            RateLimiter::clear($throttleKey);
            session()->forget('captcha_required');
            return redirect()->route('admin.home');
        }
        // Increment throttle attempts on failed login
        RateLimiter::hit($throttleKey);
        // Handle failed login
        return redirect()->route('admin.login')->with('error', 'Make sure the email or password is correct');
    }
    protected function validateCaptcha(Request $request)
    {
        $response = $request->input('g-recaptcha-response');
        // Google reCAPTCHA verification endpoint
        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret') ,
            'response' => $response,
            'remoteip' => $request->ip(),
        ]);
        $captchaSuccess = json_decode($verify);
        // Check if the reCAPTCHA verification was successful
        if (!$captchaSuccess->success) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => ['Failed to verify you are not a robot.'],
            ]);
        }
    }

    public function logout() {
        adminAuth()->logout();
        return redirect()->route('admin.login');
    }

    public function forgetPassword() {
        return view('admin.loginPages.forgetPassword');
    }

    public function resetPassword(Request $request) {
        $request->validate(['email' => 'required|email']);

        $admin = Admin::where('email', $request->input('email'))->first();

        if ($admin) {
            $token = app('auth.password.broker')->createToken($admin);
            $this->storeToken($admin->email, $token);
            $this->sendResetPasswordEmail($admin, $token);
            return back()->with('success', 'Check your email, password reset link has been sent');
        }

        return back()->with('error', 'Make sure the email is correct');
    }

    public function resetPasswordWithToken($token) {
        $tokenData = $this->getTokenData($token);

        if ($tokenData) {
            return view('admin.loginPages.resetPassword', ['data' => $tokenData]);
        }

        return redirect()->route('admin.forgotPassword');
    }

    public function updatePassword(Request $request, $token) {
        $request->validate($this->passwordValidationRules());

        $tokenData = $this->getTokenData($token);

        if ($tokenData) {
            Admin::where('email', $tokenData->email)->update(['password' => bcrypt($request->input('password'))]);
            $this->deleteToken($tokenData->email);

            adminAuth()->attempt(['email' => $tokenData->email, 'password' => $request->input('password')]);

            return redirect()->route('admin.home');
        }

        return redirect()->route('admin.forgotPassword');
    }

    public function setting() {
        return view('admin.setting');
    }

    public function setting_email(Request $request) {
        $request->validate($this->emailValidationRules());

        if (adminAuth()->user()->email === $request->input('email')) {
            adminAuth()->user()->update(['email' => $request->input('new_email')]);

            return back()->with('success', 'The email updated successfully');
        }
        return back()->with('error', 'The old email is not correct');
    }
    public function setting_password(Request $request) {
        $request->validate($this->passwordChangeValidationRules());

        if (Hash::check($request->input('password'), adminAuth()->user()->password)) {
            adminAuth()->user()->update(['password' => Hash::make($request->input('new_password'))]);

            return back()->with('success', 'The password updated successfully');
        }

        return back()->with('error', 'The old password is not correct');
    }
    private function passwordValidationRules() {
        return [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ];
    }

    private function emailValidationRules() {
        return [
            'email' => 'required|email',
            'new_email' => 'required|email',
            'email_confirmation' => 'required|email|same:new_email',
        ];
    }

    private function passwordChangeValidationRules() {
        return [
            'password' => 'required|string',
            'new_password' => 'required|string',
            'password_confirmation' => 'required|string|same:new_password',
        ];
    }

    private function storeToken($email, $token) {
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    private function sendResetPasswordEmail($admin, $token) {
        Mail::to($admin->email)->send(new AdminResetPassword(['admin' => $admin, 'token' => $token]));
    }

    private function getTokenData($token) {
        return DB::table('password_reset_tokens')
            ->where('token', $token)
            ->where('created_at', '>', Carbon::now()->subHours(2))
            ->first();
    }

    private function deleteToken($email) {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
