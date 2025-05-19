<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

// Sử dụng RedirectResponse để gợi ý kiểu trả về
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Sử dụng DB Facade
use Illuminate\Support\Facades\Log;

// Sử dụng Log Facade để ghi log lỗi (tùy chọn)
use Illuminate\Support\Facades\Validator;

// Sử dụng Validator Facade để kiểm tra dữ liệu

class SearchController extends Controller
{
    /**
     * Tìm kiếm tuyến đường dựa trên điểm bắt đầu, kết thúc và ngày đi.
     * Chuyển hướng đến trang danh sách xe nếu tìm thấy, ngược lại báo lỗi.
     *
     * @param Request $request Dữ liệu gửi lên từ form tìm kiếm.
     * @return RedirectResponse Chuyển hướng đến trang kết quả hoặc trang trước đó.
     */
    public function search_route(Request $request): RedirectResponse
    {
        // 1. --- Validation ---
        // Kiểm tra các trường dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            // location_start_id và location_end_id phải tồn tại, là chuỗi, và đúng định dạng 'type_id'
            'location_start_id' => 'required|string|different:location_end_id',
            'location_end_id' => 'required|string|different:location_start_id',
            'departure_date' => 'required|date|after_or_equal:today',
        ], [
            'location_start_id.required' => 'Vui lòng chọn điểm đi.',
            'location_end_id.required' => 'Vui lòng chọn điểm đến.',
            'location_end_id.different' => 'Điểm đến phải khác điểm đi.',
            'departure_date.required' => 'Vui lòng chọn ngày đi.',
            'departure_date.date' => 'Ngày đi không hợp lệ.',
            'departure_date.after_or_equal' => 'Ngày đi phải là hôm nay hoặc sau hôm nay.',
        ]);

        // Nếu validation thất bại, quay lại trang trước với lỗi và input cũ
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Gửi lỗi về view
                ->withInput(); // Giữ lại các giá trị đã nhập trong form
        }

        // 2. --- Xác định Province IDs ---
        // Lấy province_id cho điểm bắt đầu
        $startProvinceId = $this->getProvinceIdFromLocation($request->input('location_start_id'));
        // Lấy province_id cho điểm kết thúc
        $endProvinceId = $this->getProvinceIdFromLocation($request->input('location_end_id'));

        // Nếu không xác định được province_id (do lỗi dữ liệu hoặc DB)
        if (!$startProvinceId || !$endProvinceId) {
            Log::warning('SearchController: Could not determine province IDs.', [
                'start_location' => $request->input('location_start_id'),
                'end_location' => $request->input('location_end_id')
            ]);
            // Quay lại trang trước với thông báo lỗi chung
            return redirect()->back()
                ->with('error', 'Không thể xác định được địa điểm bạn chọn. Vui lòng thử lại.')
                ->withInput();
        }

        // Kiểm tra nếu tỉnh đi và tỉnh đến giống nhau (logic bổ sung)
        if ($startProvinceId === $endProvinceId) {
            return redirect()->back()
                ->with('error', 'Điểm đến phải thuộc tỉnh/thành phố khác với điểm đi.')
                ->withInput();
        }

        // 3. --- Tìm Tuyến đường (`routes`) ---
        try {
            // Tìm tuyến đường khớp với province_id_start và province_id_end
            $foundRoute = DB::table('routes')
                ->where('province_id_start', $startProvinceId)
                ->where('province_id_end', $endProvinceId)
                ->orderBy('priority', 'asc') // Sắp xếp theo ưu tiên (giả sử số nhỏ ưu tiên cao hơn)
                ->select('slug') // Chỉ cần lấy slug để chuyển hướng
                ->first(); // Lấy tuyến phù hợp nhất (đầu tiên sau khi sắp xếp)

        } catch (\Exception $e) {
            // Ghi log lỗi nếu có lỗi truy vấn DB
            Log::error('SearchController: Database error during route search: ' . $e->getMessage());
            // Quay lại trang trước với thông báo lỗi chung
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi trong quá trình tìm kiếm. Vui lòng thử lại sau.')
                ->withInput();
        }

        // 4. --- Chuyển hướng ---
        if ($foundRoute) {
            session()->put('departure_date', $request->input('departure_date'));
            return redirect()->route('client.bus_list', [
                'route_slug' => $foundRoute->slug,
                'departure_date' => $request->input('departure_date')
            ]);
        } else {
            // Nếu không tìm thấy tuyến đường nào phù hợp
            // Quay lại trang trước với thông báo lỗi
            return redirect()->back()
                ->with('error', 'Rất tiếc, không tìm thấy tuyến đường nào phù hợp với lựa chọn của bạn.')
                ->withInput(); // Giữ lại lựa chọn của người dùng trên form
        }
    }

    /**
     * Helper function: Lấy province_id từ location_id (định dạng 'province_X' hoặc 'district_Y').
     *
     * @param string $locationId ID của địa điểm từ input (vd: 'province_1', 'district_5').
     * @return int|null Trả về province_id nếu hợp lệ, ngược lại trả về null.
     */
    private function getProvinceIdFromLocation(string $locationId): ?int
    {
        // Kiểm tra định dạng cơ bản
        if (empty($locationId) || !str_contains($locationId, '_')) {
            Log::warning('SearchController: Invalid location ID format.', ['location_id' => $locationId]);
            return null;
        }

        // Tách loại (province/district) và ID số
        list($type, $id) = explode('_', $locationId, 2);
        $id = filter_var($id, FILTER_VALIDATE_INT); // Lấy ID số và kiểm tra

        if ($id === false || $id <= 0) {
            Log::warning('SearchController: Invalid numeric ID in location string.', ['location_id' => $locationId]);
            return null;
        }

        // Nếu là tỉnh, trả về ID luôn
        if ($type === 'province') {
            // Có thể thêm bước kiểm tra xem province ID này có tồn tại trong bảng provinces không nếu cần
            // $exists = DB::table('provinces')->where('id', $id)->exists();
            // if (!$exists) return null;
            return $id;
        } // Nếu là quận/huyện, truy vấn DB để lấy province_id
        elseif ($type === 'district') {
            try {
                $district = DB::table('districts')->where('id', $id)->select('province_id')->first();
                // Trả về province_id nếu tìm thấy district, ngược lại trả về null
                return $district?->province_id;
            } catch (\Exception $e) {
                // Ghi log lỗi nếu truy vấn DB thất bại
                Log::error('SearchController: DB error fetching province_id for district: ' . $e->getMessage(), ['district_id' => $id]);
                return null;
            }
        }

        // Nếu loại không phải 'province' hay 'district'
        Log::warning('SearchController: Unknown location type in ID string.', ['location_id' => $locationId]);
        return null;
    }
}
