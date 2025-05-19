<?php

namespace App\Http\Middleware\KingExpressBus;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientAuthMiddleware
{
    /**
     * Handle an incoming request.
     * Kiểm tra xem khách hàng đã đăng nhập (dựa vào session 'customer_id') hay chưa.
     * Nếu chưa, chuyển hướng đến trang đăng nhập.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem session 'customer_id' có tồn tại không
        if (!session()->has('customer_id')) {
            // Nếu chưa đăng nhập, lưu lại URL muốn truy cập (để redirect về sau khi đăng nhập thành công)
            // và chuyển hướng đến trang đăng nhập client
            return redirect()->route('client.login_page')->with('error', 'Vui lòng đăng nhập để truy cập trang này.');
        }

        // Nếu đã đăng nhập, cho phép request tiếp tục
        return $next($request);
    }
}
