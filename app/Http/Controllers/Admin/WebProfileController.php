<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWebProfileRequest;
use App\Http\Requests\Admin\UpdateWebProfileRequest;
use App\Models\WebProfile;
use App\Support\Admin\UploadStager;
use App\Support\ClientCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WebProfileController extends Controller
{
    private const UPLOAD_DIR = 'website';
    private const UPLOAD_CONTENT_DIR = 'website/content';

    public function index(Request $request): View
    {
        $section  = in_array($request->query('section'), ['profile', 'menus']) ? $request->query('section') : 'profile';
        $profiles = WebProfile::orderByDesc('is_default')->orderBy('profile_name')->get();

        return view('admin.website.index', compact('section', 'profiles'));
    }

    public function create(): View
    {
        return view('admin.website.profile-form', [
            'profile' => null,
        ]);
    }

    public function store(StoreWebProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data = $this->commitUploads($data, $request->session()->getId());

        $profile = WebProfile::create($data);

        if ($profile->is_default) {
            // booted() did mass-update to demote siblings — bypass observer, flush explicitly
            ClientCache::forgetWebProfile();
        }

        return redirect()
            ->route('admin.website.index', ['section' => 'profile'])
            ->with('success', 'Đã thêm cấu hình website.');
    }

    public function edit(WebProfile $profile): View
    {
        return view('admin.website.profile-form', compact('profile'));
    }

    public function update(UpdateWebProfileRequest $request, WebProfile $profile): RedirectResponse
    {
        $data = $request->validated();
        $data = $this->commitUploads($data, $request->session()->getId(), $profile);

        $profile->update($data);

        // booted() mass-updates siblings when is_default becomes true — bypass observer, flush explicitly
        if ($profile->fresh()->is_default) {
            ClientCache::forgetWebProfile();
        }

        return redirect()
            ->route('admin.website.profiles.edit', $profile)
            ->with('success', 'Đã cập nhật cấu hình website.');
    }

    public function destroy(WebProfile $profile): RedirectResponse
    {
        try {
            $profile->delete();
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.website.index', ['section' => 'profile'])
                ->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()
            ->route('admin.website.index', ['section' => 'profile'])
            ->with('success', 'Đã xóa cấu hình website.');
    }

    public function setDefault(WebProfile $profile): RedirectResponse
    {
        $profile->update(['is_default' => true]);

        // Mass update via booted() bypasses observer — flush cache explicitly
        ClientCache::forgetWebProfile();

        return redirect()
            ->route('admin.website.index', ['section' => 'profile'])
            ->with('success', "Đã đặt \"{$profile->profile_name}\" làm cấu hình mặc định.");
    }

    // -------------------------------------------------------------------------

    /**
     * Detect upload tokens and commit staged files to the public disk.
     * Existing paths (no '~' separator) pass through unchanged.
     */
    private function commitUploads(array $data, string $sessionId, ?WebProfile $existing = null): array
    {
        foreach (['logo_url', 'favicon_url'] as $field) {
            $value = $data[$field] ?? null;
            if ($value && str_contains($value, '~')) {
                $data[$field] = UploadStager::commit($value, self::UPLOAD_DIR, $sessionId);
            } elseif ($value === null && $existing?->{$field}) {
                // Field cleared — keep null; file remains on disk (no auto-delete here)
                $data[$field] = null;
            }
        }

        return $data;
    }
}
