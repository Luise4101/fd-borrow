<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Resources\Asset\BrandResource;
use App\Filament\Resources\Main\BorrowResource;
use Illuminate\Session\Middleware\StartSession;
use App\Filament\Resources\Account\RoleResource;
use App\Filament\Resources\Account\UserResource;
use App\Filament\Resources\Asset\StatusResource;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Resources\Asset\CategoryResource;
use App\Filament\Resources\Asset\SupplierResource;
use App\Filament\Resources\Asset\MigrationResource;
use App\Filament\Resources\Inventory\StoreResource;
use App\Filament\Resources\Asset\DepartmentResource;
use App\Filament\Resources\Inventory\AdjustResource;
use App\Filament\Resources\Inventory\RepairResource;
use App\Filament\Resources\Inventory\SerialResource;
use App\Filament\Resources\Account\TableListResource;
use App\Filament\Resources\Inventory\ProductResource;
use App\Filament\Resources\Main\BorrowManageResource;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\Account\PermissionResource;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use App\Filament\Resources\Account\RolePermissionResource;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->font('Sarabun')
            ->colors([
                'info' => Color::Blue,
                'gray' => Color::Gray,
                'danger' => Color::Rose,
                'warning' => Color::Orange,
                'primary' => 'rgb(29, 78, 216)'
            ])
            ->brandName('FdBorrow')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->favicon(asset('storage/icon/favicon.ico'))
            ->topNavigation()
            ->breadcrumbs(false)
            ->sidebarCollapsibleOnDesktop()
            ->navigation(function(NavigationBuilder $builder): NavigationBuilder {
                return $builder ->groups([
                    NavigationGroup::make('1.บัญชีและสิทธิ์การเข้าถึง')
                        ->items([
                            ...UserResource::getNavigationItems(),
                            ...RoleResource::getNavigationItems(),
                            ...TableListResource::getNavigationItems(),
                            ...PermissionResource::getNavigationItems(),
                            ...RolePermissionResource::getNavigationItems()
                        ]),
                    NavigationGroup::make('2.ตั้งค่าการใช้งาน')
                        ->items([
                            ...StatusResource::getNavigationItems(),
                            ...CategoryResource::getNavigationItems(),
                            ...BrandResource::getNavigationItems(),
                            ...SupplierResource::getNavigationItems(),
                            ...DepartmentResource::getNavigationItems(),
                            ...MigrationResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('3.คลังอุปกรณ์')
                        ->items([
                            ...ProductResource::getNavigationItems(),
                            ...SerialResource::getNavigationItems(),
                            ...AdjustResource::getNavigationItems(),
                            ...StoreResource::getNavigationItems(),
                            ...RepairResource::getNavigationItems()
                        ]),
                    NavigationGroup::make('4.การยืมคืนอุปกรณ์')
                        ->items([
                            ...BorrowResource::getNavigationItems(),
                            ...BorrowManageResource::getNavigationItems()
                        ])
                ]);
            })
        ;
    }
}
