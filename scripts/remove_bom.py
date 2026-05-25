import os

# Ký tự Byte Order Mark (BOM) của UTF-8
BOM = b'\xef\xbb\xbf'

# Các thư mục cần quét (có thể thêm bớt tùy ý)
TARGET_DIRS = ['app', 'resources', 'routes', 'config', 'public']

# Các định dạng file cần kiểm tra
TARGET_EXTS = ['.php', '.html', '.css', '.js', '.json', '.vue', '.jsx']

def remove_bom(filepath):
    """Đọc file dưới dạng byte, nếu có BOM ở đầu thì cắt bỏ và ghi đè lại."""
    try:
        with open(filepath, 'rb') as f:
            content = f.read()

        if content.startswith(BOM):
            content = content[3:] # Cắt bỏ 3 byte đầu tiên (BOM)
            with open(filepath, 'wb') as f:
                f.write(content)
            print(f"[CLEANED] {filepath}")
            return True
        return False
    except Exception as e:
        print(f"[ERROR] Không thể xử lý {filepath}: {e}")
        return False

def main():
    print("Bắt đầu quét và xóa BOM...")
    cleaned_count = 0

    for directory in TARGET_DIRS:
        if not os.path.exists(directory):
            print(f"[SKIP] Thư mục '{directory}' không tồn tại.")
            continue

        for root, _, files in os.walk(directory):
            for file in files:
                if any(file.endswith(ext) for ext in TARGET_EXTS):
                    filepath = os.path.join(root, file)
                    if remove_bom(filepath):
                        cleaned_count += 1

    print("-" * 30)
    print(f"Hoàn tất! Đã dọn dẹp {cleaned_count} file bị lỗi BOM.")

if __name__ == "__main__":
    main()