<li class="dd-item" data-id="{{ $item->id }}">
    <div class="dd-content">
        <div class="dd-handle">
            <i class="fas fa-grip-vertical handle-icon"></i>
        </div>
        <div class="dd-text">
            <span class="menu-name">{{ $item->name }}</span>
            @if($item->url)
                <span class="menu-url">({{ $item->url }})</span>
            @endif
        </div>
        <div class="menu-actions">
            <a href="{{ route('admin.menus.edit', ['menu' => $item->id]) }}" class="btn btn-xs btn-warning" title="Sửa">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('admin.menus.destroy', ['menu' => $item->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-danger" title="Xóa"
                        onclick="return confirm('Bạn có chắc chắn muốn xóa mục menu này? Các menu con (nếu có) cũng sẽ bị xóa.')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Nếu có menu con, render chúng trong một danh sách mới --}}
    @if (!empty($item->children) && count($item->children) > 0)
        <ol class="dd-list">
            @foreach ($item->children as $child)
                @include('kingexpressbus.admin.modules.menus.menu_item', ['item' => $child])
            @endforeach
        </ol>
    @endif
</li>
