<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHelper;
use App\Helpers\LogHelper;
use App\Models\MerchantAccount;
use App\Models\UserDevice;
use App\Models\UserSecurityAudit;
use Closure;
use Illuminate\Http\Request;
use Browser;
use Carbon\Carbon;

class UserSecurityAuditTrack
{
    private $allowedPlatform;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 'web' added for the web BFF client, which has no device/SIM identifiers.
        $this->allowedPlatform = ['android', 'ios', 'web'];
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // deviceId is required for mobile clients; the web BFF has none.
        if (!isWebPlatform($request) && !$request->header('deviceId')) {
            return ApiResponseHelper::error(__('messages.invalid_device_id'), 422);
        }
        // if (!$request->header('simId')) {
        //     return ApiResponseHelper::error(__('messages.invalid_sim_id'), 422);
        // }
        if (!$request->hasHeader('platform') || !in_array($request->header('platform'), $this->allowedPlatform)) {
            return ApiResponseHelper::error(__('messages.invalid_platform'), 422);
        }
        $authUser = auth()->user();
        $modelType = get_class($authUser);
        $userSecurityAudit = UserSecurityAudit::create([
            'user_id' => $authUser->id,
            'model_type' => $modelType,
            'device_id' => $request->header('deviceId'),
            'sim_id' => $request->header('simId') ?? null,
            'imei' => $request->header('imeiNo') ?? null,
            'google_ad_id' => $request->header('googleAdId') ?? null,
            'ip_address' => LogHelper::incomingIp(),
            'api_name' => $request->getRequestUri(),
            'api_type' => $modelType === MerchantAccount::class ? 'merchant_api' : 'crm_api',
            'user_agent' => Browser::userAgent() ?? null,
            'browser_name' => $request->header('deviceName') ?? Browser::browserName() ?? null,
            'browser_version' =>  $request->header('deviceModel') ?? Browser::browserVersion() ?? null,
            'platform' => $request->header('platform'),
            'screen_size' => $request->header('screenSize') ?? null,
            'lat' => $request->header('lat') ?? null,
            'lon' =>  $request->header('lon') ?? null
        ]);

        // Device binding is a mobile concept. For web we still write the audit
        // row above (compliance trail), but we do NOT touch user_devices —
        // its device_id is NOT NULL and the row is keyed by (user_id, model_type),
        // so a web upsert would error and/or clobber the merchant's mobile device.
        if (!isWebPlatform($request)) {
            UserDevice::updateOrCreate([
                'user_id' => $userSecurityAudit->user_id,
                'model_type' => $userSecurityAudit->model_type,
            ], [
                'device_id' => $userSecurityAudit->device_id,
                'sim_id' => $userSecurityAudit->sim_id,
                'additional' =>  $request->header('additional') ?? null,
                'api_type' => $userSecurityAudit->api_type,
                'token' => $request->bearerToken(),
                'is_verified' => 1,
                'token_generated_at' => $authUser->last_login_at,
                'token_expires_at' => Carbon::parse($authUser->last_login_at)->addDays(365),
            ]);
        }

        return $next($request);
    }
}
