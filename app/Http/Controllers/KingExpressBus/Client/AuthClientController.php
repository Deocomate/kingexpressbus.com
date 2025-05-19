<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

// Thêm RedirectResponse
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Thêm Hash Facade
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

// Thêm Rule Password cho validation mạnh hơn

class AuthClientController extends Controller
{
    /**
     * Hiển thị trang đăng nhập cho khách hàng.
     */
    public function login_page()
    {
        // Nếu đã đăng nhập rồi thì chuyển về trang chủ hoặc trang tài khoản
        if (session()->has('customer_id')) {
            return redirect()->route('client.user_information_page'); // Hoặc homepage
        }
        return view("kingexpressbus.client.modules.auth.login");
    }

    /**
     * Xử lý yêu cầu đăng nhập.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string', // Nên có min length
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->except('password'));
        }

        try {
            // Tìm khách hàng bằng email
            $customer = DB::table('customers')->where('email', $request->input('email'))->first();

            // Kiểm tra khách hàng tồn tại và đã đăng ký (có mật khẩu) chưa
            if (!$customer || !$customer->password || !$customer->is_registered) {
                return redirect()->back()->with('error', 'Email hoặc mật khẩu không chính xác.')->withInput($request->except('password'));
            }

            // Kiểm tra mật khẩu
            if (Hash::check($request->input('password'), $customer->password)) {
                // Đăng nhập thành công
                // Lưu thông tin vào session
                session([
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->fullname,
                    'customer_email' => $customer->email,
                ]);
                $request->session()->regenerate(); // Tạo lại session ID

                Log::info('Customer logged in successfully.', ['customer_id' => $customer->id]);

                // Chuyển hướng đến trang tài khoản hoặc trang trước đó (nếu có)
                // return redirect()->intended(route('client.user_information_page')); // intended() sẽ đưa về trang họ muốn vào trước khi bị bắt đăng nhập
                return redirect()->route('homepage')->with('success', 'Đăng nhập thành công!');


            } else {
                // Sai mật khẩu
                return redirect()->back()->with('error', 'Email hoặc mật khẩu không chính xác.')->withInput($request->except('password'));
            }

        } catch (\Exception $e) {
            Log::error('Customer login failed: ' . $e->getMessage(), $request->except('password'));
            return redirect()->back()->with('error', 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại.')->withInput($request->except('password'));
        }
    }

    /**
     * Hiển thị trang đăng ký.
     */
    public function register_page()
    {
        // Nếu đã đăng nhập rồi thì chuyển về trang chủ hoặc trang tài khoản
        if (session()->has('customer_id')) {
            return redirect()->route('client.user_information_page'); // Hoặc homepage
        }
        return view("kingexpressbus.client.modules.auth.register");
    }

    /**
     * Xử lý yêu cầu đăng ký.
     */
    public function register(Request $request): RedirectResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255', // Không cần unique ở đây, sẽ kiểm tra logic bên dưới
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'password' => [
                'required',
                'confirmed', // Yêu cầu có trường password_confirmation khớp với password
                Password::min(8) // Sử dụng Rule Password của Laravel (khuyến nghị)
                // ->mixedCase() // Yêu cầu chữ hoa + thường (tùy chọn)
                // ->numbers()   // Yêu cầu có số (tùy chọn)
                // ->symbols() // Yêu cầu có ký tự đặc biệt (tùy chọn)
            ],
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            // Thêm message cho các rule khác của Password nếu dùng
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            $email = $request->input('email');
            $customer = DB::table('customers')->where('email', $email)->first();

            if ($customer) {
                // Email đã tồn tại
                if ($customer->is_registered) {
                    // Đã đăng ký tài khoản rồi -> báo lỗi
                    return redirect()->back()->with('error', 'Địa chỉ email này đã được đăng ký.')->withInput($request->except('password', 'password_confirmation'));
                } else {
                    // Chưa đăng ký (khách vãng lai cũ) -> Cập nhật tài khoản
                    DB::table('customers')
                        ->where('id', $customer->id)
                        ->update([
                            'fullname' => $request->input('fullname'),
                            'phone' => $request->input('phone'),
                            'address' => $request->input('address'), // Cập nhật địa chỉ nếu có
                            'password' => Hash::make($request->input('password')), // Hash mật khẩu mới
                            'is_registered' => true, // Đánh dấu đã đăng ký
                            'updated_at' => now(),
                        ]);
                    // Lấy lại thông tin customer đã cập nhật
                    $customer = DB::table('customers')->find($customer->id);
                    Log::info('Existing guest customer registered successfully.', ['customer_id' => $customer->id]);

                }
            } else {
                // Email chưa tồn tại -> Tạo tài khoản mới
                $customerId = DB::table('customers')->insertGetId([
                    'fullname' => $request->input('fullname'),
                    'email' => $email,
                    'phone' => $request->input('phone'),
                    'address' => $request->input('address'),
                    'password' => Hash::make($request->input('password')), // Hash mật khẩu
                    'is_registered' => true, // Đánh dấu đã đăng ký
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                // Lấy lại thông tin customer vừa tạo
                $customer = DB::table('customers')->find($customerId);
                Log::info('New customer registered successfully.', ['customer_id' => $customerId]);
            }

            // Nếu không lấy được thông tin customer sau khi tạo/cập nhật -> lỗi
            if (!$customer) {
                throw new \Exception("Không thể lấy thông tin khách hàng sau khi đăng ký.");
            }

            // Đăng nhập cho người dùng mới đăng ký
            session([
                'customer_id' => $customer->id,
                'customer_name' => $customer->fullname,
                'customer_email' => $customer->email,
            ]);
            $request->session()->regenerate();

            return redirect()->route('homepage')->with('success', 'Đăng ký tài khoản thành công!');

        } catch (\Exception $e) {
            Log::error('Customer registration failed: ' . $e->getMessage(), $request->except('password', 'password_confirmation'));
            return redirect()->back()->with('error', 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại.')->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout(Request $request): RedirectResponse
    {
        Log::info('Customer logging out.', ['customer_id' => session('customer_id')]);

        // Xóa thông tin khách hàng khỏi session
        session()->forget(['customer_id', 'customer_name', 'customer_email']);

        // Vô hiệu hóa session hiện tại
        $request->session()->invalidate();

        // Tạo lại token CSRF
        $request->session()->regenerateToken();

        return redirect()->route('homepage')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
