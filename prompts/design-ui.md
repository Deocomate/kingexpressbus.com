Hãy đóng vai là một Senior Frontend Developer và Chuyên gia UI/UX Designer cấp cao. Yêu cầu bạn kích hoạt kỹ năng [UI-UX-Promax] để thực hiện nhiệm vụ thiết kế giao diện cho dự án vé xe khách "King Express Bus" (kingexpressbus.com).

1. ĐỊNH HƯỚNG DESIGN & VIBE (Tham khảo Booking.com, Traveloka):
- Đối tượng: Người dùng trẻ, Gen Z, thích sự nhanh chóng, hiện đại và trực quan.
- Phong cách: Clean, Modern, Soft-UI (bo góc mềm mại `rounded-xl` hoặc `rounded-2xl`, shadow đổ bóng mượt `shadow-soft`, `shadow-lg`).
- Container: Sử dụng chuẩn `container mx-auto px-4 max-w-7xl` để giao diện luôn canh giữa và hiển thị đẹp trên các màn hình cực lớn.
- Bảng màu chủ đạo (Tone ấm, trẻ trung):
  + Primary/Brand: `#FF9B00` (Cam đậm)
  + Secondary/Accent: `#FFE100` (Vàng tươi), `#FFC900` (Vàng cam)
  + Background/Surface: Trắng `#FFFFFF`, Xám siêu nhạt `#F8FAFC` để làm nổi bật các thẻ card.
  + Hightlight/Detail: `#EBE389` (Vàng nhạt pastel).

2. STACK CÔNG NGHỆ BẮT BUỘC:
- CSS: Kết hợp CSS qua thẻ `<style>` và sử dụng Tailwind CSS qua CDN (<script src="https://cdn.tailwindcss.com"></script>). Ghi đè config màu trực tiếp trong thẻ <script> của Tailwind. CSS thuần chỉ dùng cho các keyframes animation phức tạp hoặc custom scrollbar.
- Icon: Sử dụng FontAwesome CDN.
- Font chữ: Sử dụng font 'Inter' hoặc 'Be Vietnam Pro' từ Google Fonts.
- Javascript: 
  + Sử dụng Alpine.js (<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>) để xử lý các logic UI phức tạp (Dropdown, Tabs, Modal, Toggle) nhằm giữ code ngắn gọn, declarative thay vì viết Vanilla JS dài dòng.
  + Thư viện DatePicker: Sử dụng CDN của Litepicker hoặc Flatpickr cho thẻ input dạng Date, tùy biến CSS của thư viện này tiệp màu với màu chủ đạo `#FF9B00`.

3. YÊU CẦU TỐI ƯU TRẢI NGHIỆM NGƯỜI DÙNG [UI-UX-Promax]:
- Input Select / Dropdown: TUYỆT ĐỐI KHÔNG dùng thư viện ngoài (như Select2). Hãy TỰ CODE BẰNG TAY UI Dropdown bằng Tailwind + Alpine.js. Dropdown này phải: 
  + Có ô search bên trong để lọc danh sách.
  + Có animation trượt mở mượt mà.
  + Trạng thái hover, focus rõ ràng, làm nổi bật item đang được chọn.
- Form Elements: Các input phải có icon biểu thị (ví dụ: điểm đi, điểm đến, ngày tháng), viền focus màu `#FF9B00`, có transition mượt.
- Micro-interactions: Thêm hover effects vào thẻ xe/tuyến đường (`hover:-translate-y-1 hover:shadow-xl transition-all duration-300`), nút bấm có hiệu ứng scale nhẹ khi click (`active:scale-95`).
- Responsive: Giao diện phải hoàn hảo trên Mobile (stack dọc), Tablet và Desktop (grid nhiều cột).

4. QUY CHUẨN TÀI NGUYÊN (ASSETS):
Sử dụng ĐÚNG ĐƯỜNG DẪN ảnh có trong codebase_structure.txt. Một số đường dẫn có sẵn để bạn sử dụng:
- Logo: `/client/images/web information/logo.jpg` hoặc `/client/icons/logo.ico`
- Icon phụ: `/client/icons/pickup.svg`, `/client/icons/dropoff.svg`, `/client/icons/date.svg`
- Ảnh các dòng xe: `/client/images/kingexpressbus/cabin/1.jpg` (từ 1.jpg đến 5.jpg), `/client/images/kingexpressbus/limousine/1.png`, `/client/images/kingexpressbus/sleeper/1.jpg`...
- Ảnh điểm đến: `/client/images/city_imgs/sapa.jpg`, `/client/images/city_imgs/ha-noi.jpg`, `/client/images/city_imgs/da-nang.jpg`, `/client/images/city_imgs/ninh-binh.jpg`, `/client/images/city_imgs/hoi-an.jpg`...

5. YÊU CẦU ĐẦU RA:
- Chỉ trả về duy nhất MỘT block code HTML chứa toàn bộ. 
- Hãy setup sẵn config Tailwind trong file:
`<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: { 50: '#fffcf0', 100: '#fff7d6', 500: '#FFC900', 600: '#FF9B00', 700: '#e68a00' },
          accent: '#FFE100',
          pastel: '#EBE389'
        }
      }
    }
  }
</script>`
- Cấu trúc code phải sạch sẽ, comment rõ các vùng: HEADER, HERO/SEARCH, MAIN CONTENT (được chia grid), FOOTER.

Hãy tư duy như một kỹ sư UI/UX thực thụ, tập trung vào việc bố trí khoảng trắng (whitespace) hợp lý, phân cấp thị giác (visual hierarchy) rõ ràng, và mang lại trải nghiệm thao tác tốt nhất! Bắt đầu tạo code.

NHIỆM VỤ CỦA BẠN:

Dựa vào các component đã design lại có trong: '/resources/views/components/client' và '/resources/views/client/home/index.blade.php'

Hãy tiếp tục thiết kế lại toàn bộ: '/resources/views/client/booking'

Hạn chế review, chỉ review code bạn tạo ra, không review code của những file từ trước.