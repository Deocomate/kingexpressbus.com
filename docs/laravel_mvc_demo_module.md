# Hướng dẫn xây dựng module trong Laravel

## Giới thiệu

Đây là hướng dẫn các bước xây dựng một module trong hệ thống Laravel, bao gồm:

1. Xây dựng router trong file `routes/web.php`
2. Xây dựng Model dựa vào cấu trúc cơ sở dữ liệu
3. Xây dựng Controller kết hợp sử dụng model
4. Xây dựng file view bao gồm 2 file: `index.blade.php` và `createOrEdit.blade.php`

**Lưu ý:** Các file view nằm trong thư mục `admin/modules/{module_name}/`. Ví dụ: `admin/modules/category/index.blade.php`.

File view sử dụng component Laravel `<x-inputs.>` đã được xây dựng sẵn.

## Ví dụ minh họa: Module Category

### Cấu trúc cơ sở dữ liệu

```sql
create table categories
(
    id         bigint unsigned auto_increment primary key,
    name       varchar(255)    not null,
    thumbnail  varchar(255)    null,
    priority   int             null,
    parent_id  bigint unsigned null,
    created_at timestamp       null,
    updated_at timestamp       null,
    constraint categories_name_unique
        unique (name),
    constraint categories_parent_id_foreign
        foreign key (parent_id) references categories (id)
)
collate = utf8mb4_unicode_ci;
```

### Bước 1: Xây dựng Route

Thêm đoạn mã sau vào file `routes/web.php`:

```php
Route::resource("category", CategoryController::class);
```

### Bước 2: Xây dựng Controller

Tạo file `app/Http/Controllers/Admin/PharmacySystem/CategoryController.php`:

```php
<?php

namespace App\Http\Controllers\Admin\PharmacySystem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DB::table('categories')
            ->leftJoin('categories as parents', 'categories.parent_id', '=', 'parents.id')
            ->select('categories.*', 'parents.name as parent_name') // Lấy categories.* và parents.name
            ->get();

        return view('admin.modules.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = null;
        $categories = DB::table('categories')->get();
        return view('admin.modules.category.createOrEdit', compact('category', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories|max:255',
            'thumbnail' => 'nullable|max:255',
            'priority' => 'nullable|integer',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        DB::table('categories')->insert($validated);

        return redirect()->route('admin.category.index')->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Không cần thiết cho CRUD cơ bản
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        if (!$category) {
            abort(404);
        }
        $categories = DB::table('categories')->get();
        return view('admin.modules.category.createOrEdit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories,name,' . $id . '|max:255',
            'thumbnail' => 'nullable|max:255',
            'priority' => 'nullable|integer',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        DB::table('categories')->where('id', $id)->update($validated);

        return redirect()->route('admin.category.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('categories')->where('id', $id)->delete();
        return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully!');
    }
}
```

### Bước 3: Xây dựng View

#### 3.1 File `index.blade.php`

Tạo file `resources/views/admin/modules/category/index.blade.php`:

```php
<?php
/**
 * @var \stdClass[] $categories
 */
?>
@extends('admin.layouts.main')
@section('title','Danh sách Danh mục')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách Danh mục</h3>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <a href="{{ route('admin.category.create') }}" class="btn btn-primary mb-3">Tạo mới</a>
        <table id="data-table" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Ảnh</th>
                    <th>Danh mục cha</th>
                    <th>Thứ tự</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            @if($category->thumbnail)
                                <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" 
                                     style="max-width: 100px; max-height: 100px;">
                            @else
                                Không có
                            @endif
                        </td>
                        <td>
                            @php
                                $parentCategory = DB::table('categories')->where('id', $category->parent_id)->first();
                            @endphp
                            {{ $parentCategory ? $parentCategory->name : 'Không có' }}
                        </td>
                        <td>{{ $category->priority }}</td>
                        <td>
                            <a class="btn btn-warning" 
                               href="{{ route('admin.category.edit', ['category' => $category->id]) }}">Sửa</a>
                            <form action="{{ route('admin.category.destroy', ['category' => $category->id]) }}" 
                                  method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Bạn có chắc muốn xóa?')">Xoá
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>
    // Apply data table
    $(document).ready(function() {
        $('#data-table').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#data-table_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush
```

#### 3.2 File `createOrEdit.blade.php`

Tạo file `resources/views/admin/modules/category/createOrEdit.blade.php`:
**Yêu cầu luôn luôn sử dụng các blade input component có sẵn chứ ko dùng input DOM**

```php
<?php
$isEdit = isset($category) && $category;
?>
@extends('admin.layouts.main')
@section('title', $isEdit ? 'Sửa danh mục' : 'Tạo danh mục')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $isEdit ? 'Sửa danh mục' : 'Tạo danh mục' }}</h3>
    </div>
    <div class="card-body">
        <form
            id="categoryForm"
            action="{{ $isEdit ? route('admin.category.update', ['category' => $category->id]) : route('admin.category.store') }}"
            method="post">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <x-inputs.text label="Tên" name="name" :value="$category?->name"/>
            <x-inputs.image-link label="Ảnh đại diện" name="thumbnail" :value="$category?->thumbnail"/>
            <x-inputs.number label="Thứ tự ưu tiên" name="priority"
                             :value="$category?->priority"></x-inputs.number>

            <div class="form-group">
                <label for="parent_id">Danh mục cha</label>
                <select name="parent_id" id="parent_id" class="form-control">
                    <option value="">Không có</option>
                    @foreach($categories as $parentCategory)
                        @if(isset($category) && $category->id == $parentCategory->id)
                            @continue
                        @endif
                        <option value="{{ $parentCategory->id }}"
                            @if(isset($category) && $category->parent_id == $parentCategory->id) selected @endif>
                            {{ $parentCategory->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Sửa' : 'Tạo' }}</button>
            <a href="{{ route('admin.category.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection
```

## Tóm tắt

Khi phát triển một module CRUD trong Laravel, cần đảm bảo:
1. Đăng ký route resource trong `routes/web.php`
2. Tạo Controller với đầy đủ các phương thức CRUD
3. Tạo các file view cần thiết trong thư mục `admin/modules/{module_name}/`
    - File `index.blade.php` để hiển thị danh sách
    - File `createOrEdit.blade.php` để tạo mới và chỉnh sửa

Làm theo đúng cấu trúc này để đảm bảo tính nhất quán và dễ bảo trì của code.
