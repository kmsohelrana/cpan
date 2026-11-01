<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Services\GlobalSettingService;
use App\Http\Requests\GlobalSettingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class GlobalSettingController extends Controller
{
    public function __construct(protected GlobalSettingService $settingService) {}

    public function index()
    {
        checkAuthPermission(PermissionEnum::GLOBAL_SETTING);

        $title    = 'Global Setting';
        $settings = $this->settingService->all();

        return view('global-settings.index', compact('title', 'settings'));
    }

    public function update(GlobalSettingRequest $request): JsonResponse|RedirectResponse
    {
        if (!userHasPermission(PermissionEnum::GLOBAL_SETTING)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to update global settings.');
        }

        $validated = $request->validated();

        $validated['system_sms_enabled']   = $request->boolean('system_sms_enabled') ? '1' : '0';
        $validated['system_push_enabled']  = $request->boolean('system_push_enabled') ? '1' : '0';
        $validated['system_email_enabled'] = $request->boolean('system_email_enabled') ? '1' : '0';

        $this->settingService->set($validated);

        if ($request->ajax()) {
            return webResponse(Response::HTTP_OK, 'Settings updated successfully.');
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
