<?php
namespace VMP\Core;

defined('ABSPATH') || exit;

/**
 * Class Kernel
 *
 * Description of administrative platform component Kernel.
 *
 * @package vendor-marketplace
 */
class Kernel
{
    private Container $container;

    private array $providers = [
        '\VMP\Providers\InstallServiceProvider',
        '\VMP\Providers\CoreServiceProvider',
        '\VMP\Providers\EventServiceProvider',   // ← نظام الأحداث والمستمعين
        '\VMP\Providers\WooCommerceServiceProvider',
        '\VMP\Providers\AdminServiceProvider',
        '\VMP\Providers\VendorServiceProvider',
        '\VMP\Providers\ApiServiceProvider',
        '\VMP\Providers\CronServiceProvider',
    ];

    private array $providerInstances = [];

    /**
     *   Construct functionality helper.
     *
     * @param Container $container Description index.
     * @return void Output payload.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register functionality helper.
     *
     * @return void Output payload.
     */
    public function register(): void
    {
        foreach ($this->providers as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }

            $provider = new $providerClass($this->container);
            $this->providerInstances[] = $provider;

            if (method_exists($provider, 'register')) {
                $provider->register();
            }
        }
    }

    /**
     * Boot functionality helper.
     *
     * @return void Output payload.
     */
    public function boot(): void
    {
        // 1. InstallServiceProvider
        foreach ($this->providerInstances as $provider) {
            if ($provider instanceof \VMP\Providers\InstallServiceProvider) {
                if (method_exists($provider, 'boot')) {
                    $provider->boot();
                }
                break;
            }
        }

        // 2. WooCommerceServiceProvider
        foreach ($this->providerInstances as $provider) {
            if ($provider instanceof \VMP\Providers\WooCommerceServiceProvider) {
                if (method_exists($provider, 'boot')) {
                    $provider->boot();
                }
                break;
            }
        }

        // 3. VendorServiceProvider (يسجل الشورت كودات دائماً)
        foreach ($this->providerInstances as $provider) {
            if ($provider instanceof \VMP\Providers\VendorServiceProvider) {
                if (method_exists($provider, 'boot')) {
                    $provider->boot();
                }
                break;
            }
        }

        // 4. التحقق من WooCommerce
        $woocommerceActive = $this->container->has('woocommerce.active')
            && (bool) $this->container->make('woocommerce.active');

        // 5. باقي المزودات
        $skipClasses = [
            \VMP\Providers\InstallServiceProvider::class,
            \VMP\Providers\WooCommerceServiceProvider::class,
            \VMP\Providers\VendorServiceProvider::class,
        ];

        foreach ($this->providerInstances as $provider) {
            if (in_array(get_class($provider), $skipClasses, true)) {
                continue;
            }
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }

        // 6. تحميل الوحدات (بغض النظر عن WooCommerce)
        $this->registerModules();

        // 7. تحميل ملفات اللغة
        $this->loadTextDomain();
    }

    /**
     * RegisterModules functionality helper.
     *
     * @return void Output payload.
     */
    public function registerModules(): void
    {
        $manager = $this->container->make('module_manager');
        if (!$manager) {
            return;
        }

        $modules = [
            'vendor',        // ✅ وحدة البائع (تشمل هوكات إعادة التوجيه)
            'product',
            'order',
            'commission',
            'withdrawal',
            'subscription',
            'whatsapp',
            'template',
            'report',
            'notification',
            'settings',
            'ai',
        ];

        foreach ($modules as $module) {
            $manager->load_module($module);
        }
    }

    /**
     * LoadTextDomain functionality helper.
     *
     * @return void Output payload.
     */
    public function loadTextDomain(): void
    {
        if (function_exists('load_plugin_textdomain')) {
            load_plugin_textdomain('vmp', false, dirname(VMP_PLUGIN_BASENAME) . '/languages');
        }
    }
}
