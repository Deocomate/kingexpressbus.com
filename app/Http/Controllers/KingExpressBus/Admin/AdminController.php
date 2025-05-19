<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Thêm DB facade
use Illuminate\Support\Facades\Validator;

// Thêm Validator facade

class AdminController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa thông tin website.
     * Lấy bản ghi đầu tiên từ bảng web_info.
     * Nếu không có, tạo bản ghi mặc định rồi lấy ra.
     */
    public function index()
    {
        $webInfo = DB::table('web_info')->first(); // Lấy bản ghi đầu tiên

        // Nếu không có bản ghi nào
        if (!$webInfo) {
            // Tạo bản ghi mới với giá trị mặc định (hoặc null)
            $defaultData = [
                'logo' => null,
                'title' => 'Tiêu đề Website Mặc định',
                'description' => null,
                'email' => null,
                'phone' => null,
                'hotline' => null,
                'phone_detail' => null,
                'web_link' => null,
                'facebook' => null,
                'zalo' => null,
                'address' => null,
                'map' => null,
                'policy' => null,
                'detail' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $newId = DB::table('web_info')->insertGetId($defaultData);
            // Lấy lại bản ghi vừa tạo
            $webInfo = DB::table('web_info')->find($newId);
        }

        // Trả về view với dữ liệu webInfo
        return view("kingexpressbus.admin.modules.dashboard.edit", compact('webInfo'));
    }

    /**
     * Cập nhật thông tin website.
     */
    public function update(Request $request)
    {
        // Lấy bản ghi đầu tiên (và duy nhất) để lấy ID
        $webInfo = DB::table('web_info')->first();

        // Nếu không tìm thấy bản ghi (trường hợp hiếm hoi sau khi index đã tạo)
        if (!$webInfo) {
            return redirect()->route('admin.dashboard.index')
                ->with('error', 'Không tìm thấy cấu hình website để cập nhật.');
        }

        // --- Validation ---
        // Định nghĩa các quy tắc dựa trên bảng web_info, chủ yếu là nullable strings
        $rules = [
            'logo' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'hotline' => 'nullable|string|max:255',
            'phone_detail' => 'nullable|string|max:255',
            'web_link' => 'nullable|url|max:255', // Nên là URL
            'facebook' => 'nullable|url|max:255', // Nên là URL
            'zalo' => 'nullable|string|max:255', // Có thể là link hoặc số điện thoại
            'address' => 'nullable|string|max:255',
            'map' => 'nullable|string', // Thường là iframe hoặc text
            'policy' => 'nullable|string', // Nội dung dài
            'detail' => 'nullable|string', // Nội dung text
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('admin.dashboard.index')
                ->withErrors($validator)
                ->withInput(); // Giữ lại dữ liệu đã nhập
        }

        // Lấy dữ liệu đã được validate
        $validatedData = $validator->validated();
        $validatedData['updated_at'] = now(); // Cập nhật thời gian update

        // --- Update ---
        DB::table('web_info')
            ->where('id', $webInfo->id) // Cập nhật dựa trên ID đã lấy
            ->update($validatedData);

        // Chuyển hướng về trang index với thông báo thành công
        return redirect()->route('admin.dashboard.index')
            ->with('success', 'Cấu hình website đã được cập nhật thành công!');
    }
}
