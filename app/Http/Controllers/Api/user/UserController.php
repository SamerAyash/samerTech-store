<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use function get_api_locale;

class UserController extends Controller
{
    /**
     * Get authenticated user profile
     *
     * GET /api/user/profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json(
                new UserResource($user),
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch profile',
                'errors' => ['error' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Update user profile
     *
     * PUT /api/user/profile
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            // Update user
            $user->update($validated);
            $user->refresh();

            $locale = get_api_locale($request);
            $message = $locale === 'ar' 
                ? 'تم تحديث الملف الشخصي بنجاح' 
                : 'Profile updated successfully';

            return response()->json([
                'success' => true,
                'message' => $message,
                'profile' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'errors' => ['error' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Change user password
     *
     * POST /api/user/change-password
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            $locale = get_api_locale($request);
            $message = $locale === 'ar' 
                ? 'تم تغيير كلمة المرور بنجاح' 
                : 'Password changed successfully';

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password',
                'errors' => ['error' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Get user orders with pagination
     *
     * GET /api/user/orders
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Get pagination parameters
            $perPage = (int) $request->get('per_page', 10);
            $perPage = min(max($perPage, 1), 100); // Limit between 1 and 100
            
            // Get orders with relationships
            $orders = Order::where('user_id', $user->id)
                ->withCount('orderItems')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Format pagination response
            $pagination = [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ];

            return response()->json([
                'orders' => OrderResource::collection($orders),
                'pagination' => $pagination,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch orders',
                'errors' => ['error' => $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Get first name from full name
     *
     * @param string|null $name
     * @return string
     */
    private function getFirstName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        $parts = explode(' ', trim($name), 2);
        return $parts[0] ?? '';
    }

    /**
     * Get last name from full name
     *
     * @param string|null $name
     * @return string
     */
    private function getLastName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        $parts = explode(' ', trim($name), 2);
        return $parts[1] ?? '';
    }
}
