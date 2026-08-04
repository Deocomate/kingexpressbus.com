<?php

namespace App\Support\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Bulk-action responses shared across admin controllers.
 * The admin table JS submits bulk-delete forms via fetch() and needs a real
 * success/failure signal — a followed redirect is always HTTP 200 whether
 * DeleteGuard blocked the action or not, so AJAX callers get JSON instead.
 */
class AdminResponse
{
    public static function bulkResult(Request $request, bool $success, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $success ? 200 : 422);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}
