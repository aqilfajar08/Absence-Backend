<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function index() {
        $permissions = Permit::with('user')
        ->orderBy('id', 'desc')
        ->paginate(10);

        return view('pages.permissions.index', compact('permissions'));
    }
    public function show(Request $request, $id) {
        $permission = Permit::with('user')->find($id);
        return view('pages.permissions.show', compact('permission'));
    }

    public function edit(Request $request, $id) {
        $permission = Permit::find($id);
        return view('pages.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id) {
        $permission = Permit::find($id);
        
        // Fix the nested ternary operator with proper parentheses
        $str = $request->is_approved == 'approved' ? 'Approved' : ($request->is_approved == 'rejected' ? 'Rejected' : 'Pending');
        
        $permission->update($request->all());
        
        // Get user name for notification
        $userName = $permission->user->name ?? 'Unknown User';
        $this->sendNotificationToUser($permission->user_id, '(' . $userName . ') Status Izin: ' . $str);
        
        // Fix the redirect - remove the ID parameter since index doesn't need it
        return redirect()->route('permission.index')->with('success', 'Permission updated successfully');
    }

    public function sendNotificationToUser($userId, $message) {
        $user = User::find($userId);
        if (!$user) {
            Log::warning('FCM send skipped: user not found', ['user_id' => $userId]);
            return;
        }

        $token = $user->fcm_token;
        if (!$token) {
            Log::info('FCM send skipped: missing fcm_token for user', ['user_id' => $userId]);
            return;
        }

        $messaging = app('firebase.messaging');
        $notification = Notification::create('Absen Kasau', $message);

        $message = CloudMessage::withTarget('token', $token)
        ->withNotification($notification);

        try {
            $messaging->send($message);
        } catch (\Throwable $e) {
            Log::error('FCM send failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
