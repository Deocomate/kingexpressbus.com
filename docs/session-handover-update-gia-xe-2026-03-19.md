# Handover Phiên Update Giá Xe (2026-03-19)

## 1) Mục tiêu đã xử lý
- Đồng bộ tuyến Sapa từ file `docs/data/Các tuyến Sapa.xlsx` (đủ route, stop, trip, menu, metadata).
- Import thêm tuyến **không liên quan Sapa** từ file `C:\Users\minhlong\Downloads\King Express.xlsx`.
- Cập nhật giá cho nhóm non-Sapa theo yêu cầu mới nhất: **theo Web Price**.
- Chuẩn hóa tiếng Việt có dấu cho địa danh/điểm đón trả trong quá trình import non-Sapa.

## 2) Trạng thái DB hiện tại (snapshot)
- Thời điểm chốt: `2026-03-19` (Asia/Saigon).
- `routes`: `45`
- `trips`: `220`
- `routes` liên quan Sapa/Lào Cai: `12`
- Mẫu giá non-Sapa sau cập nhật:
  - `Hà Giang - Hà Nội`: `370000 -> 500000`
  - `Đà Nẵng - Hà Nội`: `500000 -> 1070000`
  - `Hà Nội - Huế`: `480000 -> 850000`
  - `Đà Nẵng - Huế`: `250000 -> 300000`

## 3) File/script quan trọng cần dùng ở phiên mới
- Script Sapa:
  - `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\scripts\sync_sapa_routes_from_xlsx.py`
- Script non-Sapa:
  - `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\scripts\import_non_sapa_routes_from_king_express_xlsx.py`
- Data:
  - `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\docs\data\Các tuyến Sapa.xlsx`
  - `C:\Users\minhlong\Downloads\King Express.xlsx`
- Cấu trúc ảnh tham chiếu:
  - `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\_codebase\codebase_structure.txt`

## 4) Quy tắc nghiệp vụ đã áp dụng
- Với import non-Sapa:
  - Loại toàn bộ route có `Sapa` hoặc `Lao Cai` ở đầu/cuối tuyến.
  - Map class -> bus:
    - `Sleeper`, `Cabin Single`, `Cabin Double`, `Seater`, `Limousine`, `VIP 32`, `VIP 22 Single`, `VIP 22 Double`.
  - Parse nhiều giờ chạy trong 1 ô (phân tách dấu phẩy) để tạo nhiều `trips`.
  - Giá import hiện tại tính theo logic trong script:
    - đọc `sel_price` từ cột index 11 (fallback 10)
    - `import_price = sel_price + 100000` (mô phỏng Web Price theo dataset đã dùng).
- Với Sapa sync:
  - Đồng bộ 12 tuyến trong file Sapa.
  - Xóa route không còn trong tập tuyến Sapa ở lần sync đó.
  - Rebuild `route_stops`, upsert `trips`, sync menu và dọn orphan stops.

## 5) Bảng DB bị tác động
- `routes` (upsert + metadata: title/description/duration/distance/thumbnail/image_list/content/priority)
- `trips` (upsert theo bus + giờ đi/đến; xóa trip dư)
- `stops` (tạo mới + normalize có dấu)
- `route_stops` (xóa và insert lại theo route)
- `menus` (sync menu route dưới parent “Tuyến đường”)

## 6) Quy ước ảnh đang dùng
- Ảnh thành phố: `/client/images/city_imgs/*.jpg`
- Script map theo tỉnh đích (`province_end`) để set:
  - `thumbnail_url`
  - `image_list_url` (JSON array)

## 7) Câu lệnh chạy lại nhanh
```powershell
cd C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com
python scripts\import_non_sapa_routes_from_king_express_xlsx.py
python scripts\sync_sapa_routes_from_xlsx.py
```

## 8) Tài liệu nên mở trước khi làm tiếp
- `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\README.md`
- `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\docs\system-architecture.md`
- `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\docs\codebase-summary.md`
- `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\docs\project-overview-pdr.md`
- `C:\Users\minhlong\Desktop\git-repos\kingexpressbus.com\docs\code-standards.md`

## 9) Lưu ý chuyển phiên
- `scripts/` và `docs/data/` hiện đang trạng thái untracked (`git status --short`).
- Nếu cấu trúc sheet Excel thay đổi cột giá, cần sửa lại chỉ số cột trong script non-Sapa trước khi chạy.

## Unresolved questions
- Chưa có yêu cầu xác nhận lại nguồn Web Price nếu sheet đổi format/cột (hiện dùng quy tắc `sel_price + 100000`).
- Chưa có yêu cầu cuối cùng về việc có giữ riêng 12 tuyến Sapa song song với non-Sapa trong mọi lần sync hay không (hiện DB đang giữ cả hai nhóm).
