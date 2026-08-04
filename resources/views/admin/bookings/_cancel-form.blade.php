{{--
  Cancel booking slide-over.
  Opened via Alpine event: $dispatch('open-cancel-slide-over', { bookingId: N, bookingCode: 'KEB...' })
  Submits a POST form to the cancel action route.
--}}
<div
    x-data="{
        open: false,
        bookingId: null,
        bookingCode: '',
        reason: '',
        get actionUrl() {
            return @js(route('admin.bookings.cancel', ['booking' => '__ID__'])).replace('__ID__', this.bookingId);
        },
        init() {
            window.addEventListener('open-cancel-slide-over', (e) => {
                this.bookingId  = e.detail.bookingId;
                this.bookingCode = e.detail.bookingCode ?? '';
                this.reason     = '';
                this.open       = true;
            });
        }
    }"
    class="relative z-50"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-black/30"
        x-cloak
    ></div>

    {{-- Panel --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-lg w-full">
                <div
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-full bg-white shadow-xl flex flex-col"
                    x-cloak
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">
                            Hủy vé
                            <span x-text="bookingCode ? ' — ' + bookingCode : ''" class="text-gray-500 text-sm font-normal"></span>
                        </h2>
                        <button type="button" @click="open = false"
                            class="rounded p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                            aria-label="Đóng">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form
                        :action="actionUrl"
                        method="POST"
                        class="flex-1 overflow-y-auto px-6 py-5 flex flex-col gap-5"
                    >
                        @csrf

                        <div class="rounded bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                            Hành động này không thể hoàn tác. Email thông báo sẽ được gửi đến khách hàng.
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Lý do hủy <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="cancel_reason"
                                x-model="reason"
                                required
                                class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
                            >
                                <option value="">— Chọn lý do —</option>
                                <option value="Hết chỗ trống cho chuyến xe này">Hết chỗ</option>
                                <option value="Chuyến xe tạm ngưng hoạt động trong dịp lễ tết">Lễ tết / Ngày lễ</option>
                                <option value="custom">Lý do khác</option>
                            </select>
                            @error('cancel_reason')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="reason === 'custom'" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nhập lý do cụ thể <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="custom_reason"
                                rows="4"
                                :required="reason === 'custom'"
                                class="block w-full rounded border border-gray-300 py-2 px-3 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 resize-y"
                                placeholder="Mô tả lý do hủy..."
                            ></textarea>
                            @error('custom_reason')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button
                                type="submit"
                                class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors"
                                onclick="return confirm('Xác nhận hủy đặt vé này?')"
                            >
                                Xác nhận hủy
                            </button>
                            <button type="button" @click="open = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded hover:bg-gray-50 transition-colors">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
