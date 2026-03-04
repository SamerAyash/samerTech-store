<?php 
namespace App\Http\Controllers\Api\user;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserResetPassword;

class AuthController extends Controller {
    public function login(Request $request):JsonResponse {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The email or password is incorrect'], 401);
        }
        $guestId = $request->get('guest_id');
        if ($guestId) {
            $this->migrateGuestDataToUser($guestId, $user->id);
        }
        $token = $user->createToken('api-token-xfsdhlcodso', [], now()->addDays(7))->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name'        => 'required|string|max:50',
            'last_name'         => 'required|string|max:50',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
            'country_code'     => 'required|string|max:5',
            'phone'            => 'required|string|max:20',
            'gender'           => 'required|in:male,female',
            'birth_date'       => 'required|date|before_or_equal:' . now()->subYears(18)->toDateString(),
            'country'          => 'required|string|max:50',
            'city'             => 'required|string|max:50',
            'main_address'     => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->first_name.' '.$request->last_name,
            'email'     => $request->email,
            'phone' => $request->country_code.$request->phone,
            'password'  => Hash::make($request->password),
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'country' => $request->country,
            'city' => $request->city,
            'main_address' => $request->main_address,
            'last_login_at' => Carbon::now(),
        ]);
        
        // Send notification to admins about new user
        try {
            notifyAdmins(new \App\Notifications\NewUserNotification($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send new user notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
        
        $token = $user->createToken('api-token-xfsdhlcodso', [], now()->addDays(7))->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Send password reset link to user's email
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Return success message even if user doesn't exist (security best practice)
            return response()->json([
                'message' => 'If the email exists, a password reset link has been sent.'
            ], 200);
        }

        // Generate password reset token using password broker
        $token = app('auth.password.broker')->createToken($user);
        
        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Generate reset URL for Next.js frontend
        $frontendUrl = env('FRONTEND_URL',  'http://localhost:3000');
        $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        // Send email
        try {
            Mail::to($user->email)->send(new UserResetPassword([
                'user' => $user,
                'token' => $token,
                'resetUrl' => $resetUrl
            ]));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email. Please try again later.'
            ], 500);
        }

        return response()->json([
            'message' => 'If the email exists, a password reset link has been sent.'
        ], 200);
    }

    /**
     * Reset user password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid email address.'
            ], 404);
        }

        // Check if token exists and is valid
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'message' => 'Invalid or expired reset token.'
            ], 400);
        }

        // Check if token is expired (60 minutes)
        $createdAt = Carbon::parse($tokenData->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'Reset token has expired. Please request a new one.'
            ], 400);
        }

        // Verify token (direct comparison since we store it plain)
        if ($request->token !== $tokenData->token) {
            return response()->json([
                'message' => 'Invalid reset token.'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password has been reset successfully.'
        ], 200);
    }
    private function migrateGuestDataToUser($guestId, $userId)
    {
        DB::table('carts')
            ->where('guest_id', $guestId)
            ->whereNull('user_id')
            ->update(['user_id' => $userId, 'guest_id' => null]);
    }
}
