# Laravel Blade Input Components

Đây là bộ sưu tập các component Blade tùy chỉnh cho Laravel, được thiết kế để giúp bạn xây dựng form một cách nhanh chóng, dễ dàng, và nhất quán. Các component này tuân thủ nguyên tắc DRY (Don't Repeat Yourself), giúp bạn tái sử dụng code, dễ bảo trì và mở rộng.

## Mục lục

1.  [Giới thiệu](#giới-thiệu)
2.  [Cài đặt](#cài-đặt)
3.  [Danh sách Components](#danh-sách-components)
    *   [Cơ bản](#cơ-bản)
        *   [`x-inputs.text`](#x-inputstext)
        *   [`x-inputs.email`](#x-inputsemail)
        *   [`x-inputs.number`](#x-inputsnumber)
        *   [`x-inputs.text-area`](#x-inputstext-area)
        *   [`x-inputs.select`](#x-inputsselect)
        *   [`x-inputs.select-multiple`](#x-inputsselect-multiple)
        *   [`x-inputs.date`](#x-inputsdate)
        *   [`x-inputs.time`](#x-inputstime)
        *   [`x-inputs.editor`](#x-inputseditor)
        *   [`x-inputs.image-link`](#x-inputsimage-link)
    *   [Nâng cao](#nâng-cao)
        *   [`x-inputs.text-array`](#x-inputstext-array)
        *   [`x-inputs.image-link-array`](#x-inputsimage-link-array)
        *   [`x-inputs.editor-array`](#x-inputseditor-array)
        *   [`x-inputs.text-area-array`](#x-inputstext-area-array)
4.  [Xử lý lỗi Validation](#xử-lý-lỗi-validation)
5.  [Tùy chỉnh CSS](#tùy-chỉnh-css)
6.  [Yêu cầu thư viện ngoài](#yêu-cầu-thư-viện-ngoài)
7.  [Ví dụ sử dụng tổng quát](#ví-dụ-sử-dụng-tổng-quát)
8. [License](#license)

## Giới thiệu

Các component Blade trong Laravel cho phép bạn đóng gói HTML, CSS, và JavaScript vào các thành phần tái sử dụng được. Thay vì lặp lại cùng một đoạn code HTML cho các input trong form, bạn có thể định nghĩa một component một lần và sử dụng lại nó ở bất cứ đâu trong ứng dụng. Điều này không chỉ giúp code của bạn gọn gàng hơn mà còn dễ dàng thay đổi giao diện form trên toàn bộ ứng dụng.

## Cài đặt

1.  **Yêu cầu:** Laravel (phiên bản cụ thể tùy thuộc vào phiên bản bạn đang sử dụng).
2.  **Tạo thư mục:**
    *   Tạo thư mục `app/View/Components/Inputs`.  Đây sẽ là nơi chứa các class component của bạn.
    *   Tạo thư mục `resources/views/components/inputs`. Đây là nơi chứa các view Blade tương ứng với các component.
3.  **Copy file:** Đặt các file PHP (class component) và các file Blade (view) mà bạn đã cung cấp vào đúng các thư mục vừa tạo.
4.  **Composer Autoload (Nếu cần):** Nếu class component của bạn không tự động được load, bạn có thể cần chạy lệnh sau trong terminal:

    ```bash
    composer dump-autoload
    ```

## Danh sách Components

Các component được chia thành hai nhóm: **Cơ bản** và **Nâng cao**.

### Cơ bản

#### `x-inputs.text`

*   **Mô tả:** Tạo một input text cơ bản.
*   **Class:** `App\View\Components\Inputs\Text`
*   **View:** `resources/views/components/inputs/text.blade.php`
*   **Thuộc tính:**
    *   `label` (string, bắt buộc): Nhãn của input.
    *   `name` (string, bắt buộc): Thuộc tính `name` của input (quan trọng cho việc submit form).
    *   `value` (string, tùy chọn): Giá trị mặc định.  Nếu không cung cấp, sẽ tự động lấy giá trị từ `old($name)` (dữ liệu đã nhập khi form có lỗi).
*   **Cách sử dụng:**

    ```blade
    <x-inputs.text label="Tên người dùng" name="username" value="John Doe" />
    ```
*   **Giải thích:**
    *   Component này render ra một `<div>` với class `form-group` (thường dùng trong Bootstrap).
    *   Bên trong có một `<label>` và một `<input type="text">`.
    *   Thuộc tính `id` của input được tạo động dựa trên `name` (ví dụ: `input-username`).
    *   Hiển thị thông báo lỗi validation (nếu có) dưới input.
    * Component này sẽ ưu tiên hiển thị các `value` được truyền vào, nếu không nó sẽ ưu tiên hiển thị `old($name)`, nếu cả hai đều không có thì sẽ hiển thị rỗng.

#### `x-inputs.email`

*   **Mô tả:**  Tương tự như `x-inputs.text`, nhưng là input email (`type="email"`).
*   **Class:**  `App\View\Components\Inputs\Email`
*   **View:** `resources/views/components/inputs/email.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.email label="Địa chỉ Email" name="email" value="test@example.com" />
    ```

#### `x-inputs.number`

*   **Mô tả:** Input số (`type="number"`).
*    **Class:**  `App\View\Components\Inputs\Number`
*   **View:** `resources/views/components/inputs/number.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.number label="Số lượng" name="quantity" value="10" />
    ```

#### `x-inputs.text-area`

*   **Mô tả:** Tạo một textarea.
*    **Class:**  `App\View\Components\Inputs\TextArea`
*   **View:** `resources/views/components/inputs/text-area.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text`, trừ việc không có `placeholder`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.text-area label="Mô tả" name="description" value="Đây là mô tả..." />
    ```
*   **Lưu ý:** Giá trị của textarea được đặt *bên trong* thẻ `<textarea></textarea>`.

#### `x-inputs.select`

*   **Mô tả:** Tạo một select box (dropdown) *có tích hợp sẵn Select2*.
*    **Class:**  `App\View\Components\Inputs\Select`
*   **View:** `resources/views/components/inputs/select.blade.php`
*   **Thuộc tính:**
    *   `label` (string, bắt buộc): Nhãn.
    *   `name` (string, bắt buộc): `name` của select.
    *   `attributes` (tùy chọn)
*   **Nội dung (slot):**  Các thẻ `<option>` của select box.
*   **Cách sử dụng:**

    ```blade
    <x-inputs.select label="Chọn trạng thái" name="status">
        <option value="1">Kích hoạt</option>
        <option value="0">Không kích hoạt</option>
    </x-inputs.select>
    ```
*   **Giải thích:**
    *   Sử dụng Select2 để có giao diện đẹp và tính năng tìm kiếm.
    *   **Yêu cầu:** Phải include thư viện Select2 (CSS và JS) *một lần duy nhất* trong layout của bạn (xem phần "Yêu cầu thư viện ngoài").
    *   Select2 được khởi tạo bằng JavaScript (trong `@push('scripts')`).

#### `x-inputs.select-multiple`

*   **Mô tả:** Select box cho phép chọn nhiều option (cũng dùng Select2).
*    **Class:**  `App\View\Components\Inputs\SelectMultiple`
*   **View:** `resources/views/components/inputs/select-multiple.blade.php`
*   **Thuộc tính & Cách sử dụng:**  Tương tự `x-inputs.select`, nhưng thêm thuộc tính `multiple` vào thẻ `<select>`.

    ```blade
    <x-inputs.select-multiple label="Chọn danh mục" name="categories">
        <option value="1">Tin tức</option>
        <option value="2" selected>Sản phẩm</option>
        <option value="3">Khuyến mãi</option>
    </x-inputs.select-multiple>
    ```

#### `x-inputs.date`

*   **Mô tả:**  Input ngày tháng (`type="date"`).
*    **Class:**  `App\View\Components\Inputs\Date`
*   **View:** `resources/views/components/inputs/date.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.date label="Ngày sinh" name="birthdate" />
    ```
*   **Giải thích:**
    *   Sử dụng `onfocus="this.showPicker()"` để đảm bảo trình chọn ngày hiển thị khi focus vào input.
    *   Có đoạn script để set giá trị mặc định là ngày hiện tại *nếu* không có `value` và `old($name)`.

#### `x-inputs.time`

*   **Mô tả:** Input thời gian (`type="time"`).
*    **Class:**  `App\View\Components\Inputs\Time`
*   **View:** `resources/views/components/inputs/time.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.time label="Thời gian bắt đầu" name="start_time" />
    ```
*   **Giải thích:** Tương tự như `x-inputs.date`, có script để set giá trị mặc định là giờ hiện tại.

#### `x-inputs.editor`

*   **Mô tả:**  Tạo một trình soạn thảo WYSIWYG (CKEditor 5).
*    **Class:**  `App\View\Components\Inputs\Editor`
*   **View:** `resources/views/components/inputs/editor.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text-area`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.editor label="Nội dung" name="content" />
    ```
*   **Giải thích:**
    *   **Rất quan trọng:** Yêu cầu bạn phải cài đặt và cấu hình CKEditor 5 (xem phần "Yêu cầu thư viện ngoài").
    *   Đoạn script trong `@push('scripts')` khởi tạo CKEditor trên `<textarea>`.
    *   Cấu hình CKEditor rất đầy đủ, bao gồm:
        *   Toolbar.
        *   Các plugin (link, image, table, v.v.).
        *   Hỗ trợ upload ảnh (sử dụng `ckfinder` và route `ckfinder_connector` - bạn cần cấu hình phần này).
        *   Cấu hình font, heading, v.v.
        *   Loại bỏ các plugin không cần thiết.
    *   Giá trị của editor được truyền vào/lấy ra dưới dạng HTML.

#### `x-inputs.image-link`

*   **Mô tả:** Cho phép người dùng chọn một ảnh từ CKFinder (hoặc bất kỳ trình quản lý file nào) và hiển thị preview.
*   **Class:** `App\View\Components\Inputs\ImageLink`
*   **View:** `resources/views/components/inputs/image-link.blade.php`
*   **Thuộc tính:** (Giống `x-inputs.text`)
*   **Cách sử dụng:**

    ```blade
    <x-inputs.image-link label="Ảnh đại diện" name="avatar" value="/images/default.jpg" />
    ```
*   **Giải thích:**
    *   **Yêu cầu:**  Cài đặt và cấu hình CKFinder.
    *   Input `type="text"` *chỉ đọc* (`readonly`).  Giá trị của input này sẽ là *đường dẫn* của ảnh.
    *   Nút "Duyệt Ảnh" mở CKFinder trong một popup.
    *   Khi người dùng chọn ảnh:
        *   Đường dẫn của ảnh được gán vào input.
        *   Ảnh được hiển thị trong một thẻ `<img>` để preview.
    *   Đoạn script xử lý sự kiện `files:choose` của CKFinder để lấy đường dẫn ảnh.
        *   Chú ý cách xử lý URL để lấy path (đoạn code có try-catch).

### Nâng cao

Các component nâng cao cho phép nhập dữ liệu dạng mảng.

#### `x-inputs.text-array`

*   **Mô tả:**  Cho phép nhập nhiều input text, giá trị trả về là một mảng.
*   **Class:** `App\View\Components\Inputs\TextArray`
*   **View:** `resources/views/components/inputs/text-array.blade.php`
*   **Thuộc tính:**
    *   `label` (string, bắt buộc)
    *   `name` (string, bắt buộc)
    *   `value` (array, tùy chọn): Mảng các giá trị mặc định.
*   **Cách sử dụng:**

    ```blade
    <x-inputs.text-array label="Tags" name="tags" :value="['tag1', 'tag2']" />
    ```
*   **Giải thích:**
    *   Có một input text chính để người dùng nhập.
    *   Nút "Thêm" sẽ thêm giá trị từ input này vào một danh sách (dùng `<ul>`, `<li>`).
    *   Mỗi `<li>` chứa:
        *   Một input text *ẩn* (`type="hidden"`) để lưu giá trị (có `name="{{ $name }}[]"`).
        *   Một nút "Xoá" để xóa item đó khỏi danh sách.
    *   JavaScript xử lý việc thêm/xóa các item trong danh sách.
        * Chú ý: phải đảm bảo cập nhật lại chỉ số sau mỗi lần xóa, nếu các phần tử có chứa chỉ số.

#### `x-inputs.image-link-array`

*   **Mô tả:** Tương tự như `x-inputs.text-array`, nhưng cho phép chọn nhiều ảnh từ CKFinder và hiển thị preview.
*   **Class:** `App\View\Components\Inputs\ImageLinkArray`
*   **View:** `resources/views/components/inputs/image-link-array.blade.php`
*    **Thuộc tính:**
    *   `label` (string, bắt buộc)
    *   `name` (string, bắt buộc)
    *   `value` (array, tùy chọn): Mảng các giá trị mặc định.
*   **Cách sử dụng:**
     ```blade
    <x-inputs.image-link-array label="Thư viện ảnh" name="gallery" :value="['/path/to/image1.jpg', '/path/to/image2.jpg']"></x-inputs.image-link-array>
    ```
*   **Giải thích:** (Rất giống `x-inputs.text-array`, nhưng dùng CKFinder để chọn ảnh).

#### `x-inputs.editor-array`

*   **Mô tả:**  Tạo nhiều trình soạn thảo CKEditor.
*   **Class:** `App\View\Components\Inputs\EditorArray`
*   **View:** `resources/views/components/inputs/editor-array.blade.php`
*    **Thuộc tính:**
    *   `label` (string, bắt buộc)
    *   `name` (string, bắt buộc)
    *   `value` (array, tùy chọn): Mảng các giá trị mặc định.
*   **Cách sử dụng:**

    ```blade
    <x-inputs.editor-array label="Nội dung bài viết" name="articles" :value="['<p>Nội dung 1</p>', '<p>Nội dung 2</p>']" />
    ```
*   **Giải thích:**
    *   Nút "Thêm" tạo một `<li>` mới, bên trong có:
        *   Một `<textarea>` (sẽ được CKEditor biến thành trình soạn thảo).
        *   Một nút "Xoá".
    *   JavaScript:
        *   Khởi tạo CKEditor *cho từng* `<textarea>` mới được tạo.
        *   Xử lý việc xóa:
            *   Hủy (destroy) instance CKEditor tương ứng.
            *   Xóa `<li>`.
        * Lưu mảng các instance của CKEditor trong `ckeditorInstances`

#### `x-inputs.text-area-array`

*   **Mô tả:**  Cho phép nhập nhiều textarea.
*   **Class:** `App\View\Components\Inputs\TextAreaArray`
*   **View:** `resources/views/components/inputs/text-area-array.blade.php`
*    **Thuộc tính:**
    *   `label` (string, bắt buộc)
    *   `name` (string, bắt buộc)
    *   `value` (array, tùy chọn): Mảng các giá trị mặc định.
*   **Cách sử dụng:**

    ```blade
    <x-inputs.text-area-array label="Ghi chú" name="notes" :value="['Ghi chú 1', 'Ghi chú 2']" />
    ```
* **Giải thích:**
    *   Nút "Thêm" tạo một `<li>` mới, bên trong có:
        *   Một số thứ tự.
        *   Một `<textarea>`.
        *   Một nút "Xoá".
    *   JavaScript:
        *   Thêm các phần tử vào danh sách.
        *   Xử lý việc xóa và cập nhật lại số thứ tự.

## Xử lý lỗi Validation

Tất cả các component đều hiển thị thông báo lỗi validation của Laravel (nếu có) bằng cách sử dụng:

```blade
@error($name)
    <div class="text-danger">{{ $message }}</div>
@enderror
```

Laravel tự động thêm các biến `$errors` vào view khi có lỗi.  `@error($name)` kiểm tra xem có lỗi nào liên quan đến input có `name` là `$name` không.
