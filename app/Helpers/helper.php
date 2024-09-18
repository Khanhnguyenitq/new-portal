<?php

use App\Models\Language;
use App\Models\Setting;
use PhpParser\Node\Expr\Cast\String_;

/** format news tags */

function formatTags(array $tags): String
{
   return implode(',', $tags);
}

/** get selected language from session */
function getLangauge(): string
{
    if(session()->has('language')){
        return session('language');
    }else {
        try {
            $language = Language::where('default', 1)->first();
            setLanguage($language->lang);

            return $language->lang;
        } catch (\Throwable $th) {
            setLanguage('en');

            return $language->lang;
        }
    }
}

/** set language code in session */
function setLanguage(string $code): void
{
    session(['language' => $code]);
}

/** Truncate text */

function truncate(string $text, int $limit = 45): String
{
    return \Str::limit($text, $limit, '...');
}

function truncateSafe($text, $limit = 100)
{
    // Loại bỏ các thẻ HTML trước khi cắt ngắn
    $cleanText = strip_tags($text);

    // Kiểm tra nếu độ dài chuỗi đã nhỏ hơn hoặc bằng giới hạn, thì trả về chuỗi ban đầu
    if (mb_strlen($cleanText) <= $limit) {
        return $text;
    }

    // Cắt ngắn chuỗi tới giới hạn
    $truncatedText = mb_substr($cleanText, 0, $limit);

    // Tìm vị trí khoảng trắng cuối cùng trong chuỗi cắt ngắn
    $lastSpace = mb_strrpos($truncatedText, ' ');

    // Nếu tìm thấy khoảng trắng, cắt chuỗi tới khoảng trắng đó
    if ($lastSpace !== false) {
        $truncatedText = mb_substr($truncatedText, 0, $lastSpace);
    }

    // Thêm "..." vào cuối chuỗi
    return $truncatedText . '...';
}

/** Convert a number in K format */

function convertToKFormat(int $number): String
{
    if($number < 1000){
        return $number;
    }elseif($number < 1000000){
        return round($number / 1000, 1) . 'K';
    }else {
        return round($number / 1000000, 1). 'M';
    }
}

/** Make Sidebar Active */

function setSidebarActive(array $routes): ?string
{
    foreach($routes as $route){
        if(request()->routeIs($route)){
            return 'active';
        }
    }
    return '';
}

/** get Setting */

function getSetting($key){
    $data = Setting::where('key', $key)->first();
    return $data->value;
}

/** check permission */

function canAccess(array $permissions){

   $permission = auth()->guard('admin')->user()->hasAnyPermission($permissions);
   $superAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');

   if($permission || $superAdmin){
    return true;
   }else {
    return false;
   }

}

/** get admin role */

function getRole(){
    $role = auth()->guard('admin')->user()->getRoleNames();
    return $role->first();
}

/** check user permission */

function checkPermission(string $permission){
    return auth()->guard('admin')->user()->hasPermissionTo($permission);
}
