<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // ループ回避 → 編集・更新は通す
        if ($request->routeIs('profile.edit', 'profile.update')) {
            return $next($request);
        }

        // 必須項目が未入力なら、初回設定に誘導する
        if ($user->needsProfileSetup()) {
            return redirect()
                ->route('profile.edit', ['return_to' => $request->fullUrl()])
                ->with('status', '初回設定：プロフィールを入力してください');
        }

        return $next($request);
    }
}
