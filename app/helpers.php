<?php

use Illuminate\Support\Carbon;

if (!function_exists('systemDetails')) {
    function systemDetails()
    {
        return [
            'name' => 'GEZUX',
            'version' => '1.0.0',
            'build_version' => '1.0.0',
            'real_name' => 'GEZUX Core Engine',
        ];
    }
}

if (!function_exists('menuActive')) {
    function menuActive($routeName, $type = 1, $param = null)
    {
        $class = ($type == 2) ? 'sidebar-submenu__open' : 'active';

        if (is_array($routeName)) {
            foreach ($routeName as $value) {
                if (request()->routeIs($value)) {
                    return $class;
                }
            }
        } elseif (request()->routeIs($routeName)) {
            return $class;
        }

        return '';
    }
}

if (!function_exists('siteLogo')) {
    function siteLogo()
    {
        $custom = \App\Models\AuthSetting::get('site_logo');
        return $custom ? asset($custom) : asset('images/gezux-favicon.jpg');
    }
}

if (!function_exists('siteFavicon')) {
    function siteFavicon()
    {
        $custom = \App\Models\AuthSetting::get('site_favicon');
        return $custom ? asset($custom) : asset('images/gezux-favicon.jpg');
    }
}

if (!function_exists('showAmount')) {
    function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
    {
        $separator = $separate ? ',' : '';
        $printAmount = number_format($amount, $decimal, '.', $separator);
        if ($exceptZeros) {
            $exp = explode('.', $printAmount);
            if ($exp[1] * 1 == 0) {
                $printAmount = $exp[0];
            }
        }
        return $printAmount;
    }
}

if (!function_exists('diffForHumans')) {
    function diffForHumans($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('formatStatusTitle')) {
    function formatStatusTitle($status)
    {
        $map = [
            'pending' => 'Pending Review',
            'under_review' => 'Under Review',
            'acct_pending_review' => 'Pending Review',
            'submitted' => 'Submitted',
            'approved_by_brand' => 'Approved (Bank Queued)',
            'approved_pending_payout' => 'Approved (Bank Queued)',
            'APPROVED_FOR_PAYOUT' => 'Approved for Payout',
            'PENDING_ADMIN_REVIEW' => 'Pending Admin Review',
            'processing' => 'Processing',
            'PROCESSING' => 'Processing',
            'verified' => 'Verified & Queued',
            'paid' => 'Paid to Bank',
            'PAID' => 'Paid to Bank',
            'completed' => 'Completed',
            'COMPLETED' => 'Completed',
            'rejected' => 'Rejected',
            'REJECTED' => 'Rejected',
            'on_hold' => 'On Hold',
            'ON_HOLD' => 'On Hold',
            'failed' => 'Failed',
            'active' => 'Active',
            'paused' => 'Paused',
            'cancelled' => 'Cancelled',
            'draft' => 'Draft',
        ];

        return $map[$status] ?? ucwords(str_replace(['_', '-'], ' ', (string) $status));
    }
}

if (!function_exists('showStatusBadge')) {
    function showStatusBadge($status)
    {
        $title = formatStatusTitle($status);
        $s = strtolower((string) $status);

        if (str_contains($s, 'paid') || str_contains($s, 'complete')) {
            $class = 'badge bg-success text-white';
            $icon = 'las la-check-circle';
        } elseif (str_contains($s, 'approved') || str_contains($s, 'verified')) {
            $class = 'badge bg-emerald-100 text-success border border-success';
            $icon = 'las la-check';
        } elseif (str_contains($s, 'reject') || str_contains($s, 'cancel') || str_contains($s, 'fail')) {
            $class = 'badge bg-danger text-white';
            $icon = 'las la-times-circle';
        } elseif (str_contains($s, 'hold') || str_contains($s, 'pause')) {
            $class = 'badge bg-secondary text-white';
            $icon = 'las la-pause-circle';
        } else {
            // Pending / under review
            $class = 'badge bg-warning text-dark';
            $icon = 'las la-clock';
        }

        return '<span class="' . $class . ' px-2.5 py-1 fw-bold rounded-pill" style="font-size: 11.5px; letter-spacing: 0.3px;"><i class="' . $icon . ' me-1"></i>' . e($title) . '</span>';
    }
}

if (!function_exists('getCmsMenu')) {
    function getCmsMenu($slug)
    {
        $menu = \App\Models\CmsMenu::where('slug', $slug)->with(['items' => function($q) {
            $q->orderBy('order', 'asc');
        }])->first();

        return $menu ? $menu->items : collect();
    }
}

if (!function_exists('safeRoute')) {
    function safeRoute($name, $parameters = [], $fallback = null)
    {
        try {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name, $parameters);
            }
        } catch (\Throwable $e) {}

        if ($fallback) {
            return url($fallback);
        }

        // Generic fallback from route name: e.g. admin.notifications.all -> /admin/notifications/send-all or /admin/notifications/all
        $slug = str_replace('.', '/', $name);
        return url('/' . $slug);
    }
}
