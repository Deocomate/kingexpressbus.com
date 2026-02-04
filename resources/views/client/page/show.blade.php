<x-client.layout :title="$title ?? __('client.page.meta_title')" :description="$description ?? ''">
    @php
        $updatedAt = $page->updated_at ?? null;
        $displayUpdatedAt = $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('d/m/Y H:i') : null;
    @endphp

    <section class="bg-slate-900 text-white py-16">
        <div class="container mx-auto px-4 space-y-4">
            <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                <i class="fa-solid fa-book-open"></i>
                Thong tin King Express Bus
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold">{{ $page->title ?? 'Trang noi dung' }}</h1>
            @if ($displayUpdatedAt)
                <p class="text-white/70 text-sm">Cap nhat lan cuoi: {{ $displayUpdatedAt }}</p>
            @endif
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-10">
            <article class="lg:col-span-8 space-y-6">
                <div class="prose prose-lg max-w-none text-slate-800">
                    {!! $page->content !!}
                </div>
            </article>
            <aside class="lg:col-span-4 space-y-6">
                <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">Thong tin ho tro</h2>
                    <p class="text-sm text-slate-600">
                        Can ho tro them? Chung toi luon san sang dong hanh cung ban tren moi hanh trinh.
                    </p>
                    <a href="{{ route('client.contact') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-500 transition">
                        <i class="fa-solid fa-headset"></i>
                        Lien he ho tro
                    </a>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-white p-6 space-y-4 text-sm text-slate-600">
                    <h3 class="text-base font-semibold text-slate-900">Trang pho bien</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('client.page.show', ['slug' => 'gioi-thieu']) }}"
                                class="text-blue-600 hover:text-blue-700">Gioi thieu</a></li>
                        <li><a href="{{ route('client.page.show', ['slug' => 'chinh-sach']) }}"
                                class="text-blue-600 hover:text-blue-700">Chinh sach ho tro</a></li>
                        <li><a href="{{ route('client.home') }}" class="text-blue-600 hover:text-blue-700">Trang chu</a>
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
</x-client.layout>
