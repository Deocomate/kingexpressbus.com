<div class="action-btns">
    <button class="action-btn btn-view view-btn" data-id="{{ $bus->id }}" title="Xem nhanh">
        <i class="fas fa-eye"></i>
    </button>
    <button class="action-btn btn-edit edit-btn" data-id="{{ $bus->id }}" title="Chỉnh sửa">
        <i class="fas fa-pencil-alt"></i>
    </button>
    <button class="action-btn btn-duplicate duplicate-btn" data-id="{{ $bus->id }}" title="Nhân bản">
        <i class="fas fa-copy"></i>
    </button>
    <button class="action-btn btn-delete delete-btn" data-id="{{ $bus->id }}" title="Xóa">
        <i class="fas fa-trash"></i>
    </button>
</div>
