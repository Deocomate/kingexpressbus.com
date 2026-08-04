{{-- Alpine-powered toast host. Toasts appear top-right, auto-dismiss after 4 seconds. --}}
<div
    x-data
    x-show="$store.toast.messages.length > 0"
    class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-80 pointer-events-none"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="msg in $store.toast.messages" :key="msg.id">
        <div
            x-show="msg.visible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            :class="{
                'bg-green-50 border-green-400 text-green-800': msg.type === 'success',
                'bg-red-50 border-red-400 text-red-800': msg.type === 'error',
                'bg-amber-50 border-amber-400 text-amber-800': msg.type === 'warning',
                'bg-blue-50 border-blue-400 text-blue-800': msg.type === 'info',
            }"
            class="flex items-start gap-3 p-4 rounded border shadow-sm pointer-events-auto"
            role="alert"
        >
            <p class="text-sm flex-1" x-text="msg.text"></p>
            <button
                type="button"
                @click="$store.toast.dismiss(msg.id)"
                class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                aria-label="Đóng"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        </div>
    </template>
</div>
