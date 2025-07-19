<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من أن المستخدم مسجل دخول
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // يمكنك تعديل هذا حسب نظام الأدوار لديك
        // حالياً نسمح لأي مستخدم مسجل بالوصول
        // يمكنك تغيير هذا ليتناسب مع نظام الأدوار لديك
        return $next($request);
    }
} 