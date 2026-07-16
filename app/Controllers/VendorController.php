<?php
namespace VMP\Controllers;

defined('ABSPATH') || exit;

use VMP\Services\VendorService;
use VMP\Http\Requests\RegisterVendorRequest;
use VMP\Http\Responses\SuccessResponse;
use VMP\Http\Responses\ApiResponse;

/**
 * Class VendorController
 *
 * Description of administrative platform component VendorController.
 *
 * @package vendor-marketplace
 */
class VendorController extends BaseController
{
    public function __construct(
        private VendorService $vendorService
    ) {}

    /**
     * تسجيل بائع جديد
     */
    public function registerVendor(RegisterVendorRequest $request): ApiResponse
    {
        // 1. تحويل الـ Request إلى DTO
        $dto = $request->toDTO();

        // 2. معالجة العمليات عبر طبقة الخدمة
        $vendor = $this->vendorService->registerVendor($dto);

        // 3. إعادة استجابة ناجحة
        return new SuccessResponse(
            data: $vendor->toArray(),
            message: __('تم تقديم طلب التسجيل بنجاح، يرجى الانتظار لحين المراجعة.', 'vmp')
        );
    }

    /**
     * تحديث الملف الشخصي للبائع
     * 
     * @todo إنشاء UpdateVendorProfileRequest وتمريره هنا لاحقاً
     */
    public function updateProfile(): ApiResponse
    {
        // مجرد Placeholder لنطبق نمط التسجيل على باقي الدوال في المرحلة القادمة
        return new SuccessResponse(message: 'Coming soon');
    }

    /**
     * الموافقة على بائع من قبل المشرف
     * 
     * @todo إنشاء AdminApproveVendorRequest وتمريره هنا لاحقاً
     */
    public function adminApprove(): ApiResponse
    {
        // مجرد Placeholder
        return new SuccessResponse(message: 'Coming soon');
    }

    /**
     * رفض بائع من قبل المشرف
     * 
     * @todo إنشاء AdminRejectVendorRequest وتمريره هنا لاحقاً
     */
    public function adminReject(): ApiResponse
    {
        // مجرد Placeholder
        return new SuccessResponse(message: 'Coming soon');
    }
}
