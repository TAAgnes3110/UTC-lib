<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Enums\RoleType;

class Helpers
{
    public static $responsive = ['xs' => 0, 'sm' => 576, 'md' => 768, 'lg' => 992, 'xl' => 1200, 'xxl' => 1400];
    /**
     * @todo Kiểm tra chuỗi có phải JSON hợp lệ không
     * @param mixed $str Chuỗi cần kiểm tra
     * @return bool
     */
    public static function isJson($str): bool
    {
        if ($str) {
            if (is_numeric($str))
                return false;
            if (is_string($str)) {
                return !is_null(json_decode($str));
            }
        }
        return false;
    }
    /**
     * @todo Tạo chuỗi ngẫu nhiên chỉ chữ thường (a-z)
     * Sử dụng để tạo tên file ngẫu nhiên, mã code
     * @param int $length Độ dài chuỗi
     * @param string $prefix Tiền tố
     * @return string
     */
    public static function generateRandomOnlyString($length = 10, $prefix = '')
    {
        $characters       = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString     = $prefix;
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * @todo Tạo chuỗi ngẫu nhiên (chữ + số + ký tự đặc biệt)
     * Sử dụng để tạo password, token, mã xác thực
     * @param int $length Độ dài chuỗi
     * @param string $prefix Tiền tố
     * @return string
     */
    public static function generateRandomString($length = 10, $prefix = '')
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = $prefix;
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * @todo Tạo số ngẫu nhiên
     * Sử dụng để tạo mã barcode, ISBN, số thẻ thư viện
     * @param int $length Độ dài số
     * @param string $prefix Tiền tố
     * @return string
     */
    public static function generateRandomNumber($length = 10, $prefix = ''): string
    {
        $characters       = '0123456789';
        $charactersLength = strlen($characters);
        $randomString     = $prefix;
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * @todo Tạo username duy nhất từ tên
     * Sử dụng khi đăng ký user mới, tự động tạo username không trùng
     * @param string $name Tên người dùng
     * @return string Username đã được kiểm tra không trùng
     */
    public static function generateUsername($name)
    {
        if (!$name) {
            $name = self::generateRandomOnlyString(8);
        } else {
            $name = self::toAccount($name, '');
        }
        $ok = false;
        $username = $name;
        while (!$ok) {
            if (DB::table(User::$tableName)->where('username', $username)->exists()) {
                $username = $name . '_' . self::generateRandomNumber(4);
            } else {
                $ok = true;
            }
        }
        return $username;
    }
    public static function getUnique($length = 3)
    {
        $tbl = 'unique_' . $length;
        $code = DB::table($tbl)->value('code');
        if ($code) {
            DB::table($tbl)->where('code', '=', $code)->delete();
            return $code;
        } else {
            if ($length == 5 || $length == 6 || $length == 9) {
                if (self::updateUnique($length)) {
                    $code = DB::table($tbl)->value('code');
                    if ($code) {
                        DB::table($tbl)->where('code', '=', $code)->delete();
                        return $code;
                    }
                }
            }
        }
        return '';
    }
    public static function updateUnique($length = 5): bool
    {
        $prefix = '';
        if ($length == 9) {
            $prefix = DB::table('unique_3')->value('code');
        }
        $files = Storage::disk()->files("zzz/$length");
        if (!$files) {
            if ($length == 9) {
                if (!Storage::disk()->exists($prefix)) {
                    Storage::disk()->makeDirectory($prefix);
                    File::copyDirectory(Storage::disk()->path('zzz/uniques'), Storage::disk()->path('zzz/' . $prefix));
                    $files = Storage::disk()->files('zzz/' . $prefix);
                } else {
                    $files = Storage::disk()->files("zzz/$prefix");
                    if (!$files) {
                        DB::table('unique_3')->where('code', '=', $prefix)->delete();
                        Storage::disk()->deleteDirectory("zzz/$prefix");
                        $prefix = DB::table('unique_3')->value('code');
                        Storage::disk()->makeDirectory("zzz/$prefix");
                        File::copyDirectory(Storage::disk()->path('zzz/uniques'), Storage::disk()->path("zzz/$prefix"));
                        $files = Storage::disk()->files("zzz/$prefix");
                    }
                }
            } else {
                return false;
            }
        }
        if ($files) {
            if ($length == 9) {
                $prefix .= '-';
            }
            foreach ($files as $file_name) {
                $file = fopen(Storage::disk()->path($file_name), "r");
                $lines = [];
                while (!feof($file)) {
                    $line = trim(fgets($file));
                    if (!str_contains($line, 'xxx')) {
                        $lines[] = ['code' => $prefix . $line];
                    }
                }
                fclose($file);
                if ($lines) {
                    DB::table("unique_$length")->insert($lines);
                    Storage::disk()->delete($file_name);
                    break;
                }
            }
            return true;
        }
        return false;
    }
    public static function generatePersonUsername($person_id, $prefix): string
    {
        $username = strtolower($prefix) . substr((100000000 + $person_id), 1);
        if (!DB::table(User::$tableName)->where('username', '=', $username)->exists())
            return $username;
        $maxUser = DB::table(User::$tableName)->max('id');
        $username = strtolower($prefix) . substr((100000000 + $person_id + $maxUser), 1);
        if (!DB::table(User::$tableName)->where('username', '=', $username)->exists())
            return $username;
        $ok = false;
        while (!$ok) {
            $username = strtolower($prefix) . substr((100000000 + (float)$person_id + (float)$maxUser + (float)self::generateRandomNumber(6)), 1);
            if (!DB::table(User::$tableName)->where('username', '=', $username)->exists())
                $ok = true;
        }
        return $username;
    }
    /**
     * @todo Chuyển chuỗi tiếng Việt thành slug URL-friendly
     * Sử dụng để tạo URL slug cho sách, danh mục (vd: "Sách hay" -> "sach-hay")
     * @param string $str Chuỗi tiếng Việt
     * @return string Slug (chữ thường, không dấu, dùng dấu -)
     */
    public static function toSlug($str): string
    {
        $str = mb_strtolower($str);
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = preg_replace('/[^a-z0-9]/', ' ', $str);
        return preg_replace('/\s+/', '-', trim($str));
    }
    /**
     * @todo Chuyển chuỗi tiếng Việt thành tên tài khoản (không dấu, có thể có khoảng trắng)
     * Sử dụng để tạo username, tên file từ tên tiếng Việt
     * @param string $str Chuỗi tiếng Việt
     * @param string $sp Ký tự thay thế khoảng trắng ('' = giữ nguyên, '-' = dùng dấu gạch)
     * @return string
     */
    public static function toAccount($str, $sp = ''): string
    {
        $str = mb_strtolower($str);
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = preg_replace('/[^a-z0-9]/', ' ', $str);
        return preg_replace('/\s+/', $sp, trim($str));
    }
    public static function jsonDecode($str)
    {
        $results = json_decode($str, true);
        if ($results) {
            foreach ($results as &$result) {
                if (self::isJson($result)) {
                    $result = self::jsonDecode($result);
                }
            }
        }
        return $results;
    }
    public static function setParamsHistories($inputs, $action)
    {
        $params = [];
        if (self::isJson($inputs)) {
            $params = self::jsonDecode($inputs);
        } elseif (is_object($inputs)) {
            $params = (array)$inputs;
        } elseif (is_array($inputs)) {
            $params = $inputs;
        }
        if (!empty($params['histories'])) {
            $params['histories'][] = ['action' => $action, 'by' => Auth::id(), 'at' => date('d/m/Y H:i:s')];
        }
        return $params;
    }
    /**
     * @todo Cắt ngắn text, loại bỏ HTML tags
     * Sử dụng để hiển thị mô tả ngắn sách, preview nội dung
     * @param string $input Text cần cắt
     * @param int|string $length Độ dài tối đa ('full' = không cắt)
     * @param bool $ellipses Thêm "..." nếu bị cắt
     * @param bool $strip_tag Loại bỏ HTML tags
     * @param bool $strip_style Loại bỏ inline styles
     * @return string
     */
    public static function trimText($input, $length, $ellipses = true, $strip_tag = true, $strip_style = true)
    {
        //strip tags, if desired
        if ($strip_tag) {
            $input = strip_tags($input);
        }

        //strip tags, if desired
        if ($strip_style) {
            $input = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $input);
        }

        if ($length == 'full') {
            $trimmed_text = $input;
        } else {
            $input = str_replace(['&nbsp;', '\r\n', '\r', '\n'], ' ', $input);
            //no need to trim, already shorter than trim length
            if (strlen($input) <= $length) {
                return $input;
            }
            $last_space = strrpos(substr($input, 0, $length), ' ');
            $trimmed_text = substr($input, 0, $last_space);
            if ($ellipses) {
                $trimmed_text .= '...';
            }
        }

        return $trimmed_text;
    }
    public static function pluck($arr, $key): array
    {
        $results = [];
        if ($arr) {
            foreach ($arr as $item) {
                if ($item) {
                    if (is_object($item)) {
                        if (property_exists($item, $key)) {
                            if ($item->$key) {
                                $results[] = $item->$key;
                            }
                        }
                    } elseif (is_array($item)) {
                        if (isset($item[$key])) {
                            $results[] = $item[$key];
                        }
                    }
                }
            }
        }
        return $results;
    }
    public static function ArrInteger($arr): array
    {

        if ($arr) {
            if (is_array($arr)) {
                return array_map(function ($value) {
                    return (int)$value;
                }, $arr);
            } else {
                return [(int)$arr];
            }
        }
        return [];
    }
    public static function ArrString($arr): array
    {
        if ($arr) {
            if (is_array($arr)) {
                return array_map(function ($value) {
                    return (string)$value;
                }, $arr);
            } else {
                return [(string)$arr];
            }
        }
        return [];
    }
    public static function ArrMerge($arr, $arr2): array
    {
        if ($arr) {
            if ($arr2) {
                foreach ($arr2 as $value) {
                    if (!in_array($value, $arr)) {
                        $arr[] = $value;
                    }
                }
            }
        } else {
            return $arr2;
        }
        return $arr;
    }
    public static function ArrDiff($arr, $arr2): array
    {
        if ($arr) {
            if ($arr2) {
                return array_merge(array_diff($arr, $arr2), array_diff($arr2, $arr));
            } else {
                return $arr;
            }
        } else {
            if ($arr2) {
                return $arr2;
            }
        }
        return  [];
    }
    public static function pluckNotEmpty($arr, $key): array
    {
        $results = [];
        if ($arr) {
            foreach ($arr as $item) {
                if ($item) {
                    if (is_object($item)) {
                        if (property_exists($item, $key)) {
                            if (!empty($item->$key)) {
                                $results[] = $item->$key;
                            }
                        }
                    } elseif (is_array($item)) {
                        if (!empty($item[$key])) {
                            $results[] = $item[$key];
                        }
                    }
                }
            }
        }
        return $results;
    }
    public static function phoneRegex($validator = false): string
    {
        if ($validator)
            return 'regex:/((849|843|847|848|845|09|02|03|07|08|05)+([0-9]{8,9})\b)/';
        return '/((849|843|847|848|845|09|02|03|07|08|05)+([0-9]{8,9})\b)/';
    }

    public static function templateStyleRender($settings): string
    {
        $style = '';
        $style_el = '';
        if ($settings) {
            if (is_array($settings)) {
                $settings = (object)$settings;
            }
            if (!empty($settings->background)) {
                if (!empty($settings->background->color)) {
                    $style .= '--bs-body-bg:' . $settings->background->color . ';';
                }
            }
            if (!empty($settings->background_image)) {
                if (!empty($settings->background_image->status)) {
                    $style .= 'background-image: url("' . $settings->background_image->file_url . '/' . $settings->background_image->full_name . '");';
                }
            }
            if (!empty($settings->img_logo)) {
                if (!empty($settings->img_logo->status)) {
                    $style .= '--site-logo: url("' . $settings->img_logo->file_url . '/' . $settings->img_logo->full_name . '");';
                }
            }
            if (!empty($settings->primary_color)) {
                $style .= '--bs-primary: ' . $settings->primary_color . ';';
            }
            if (!empty($settings->second_color)) {
                $style .= '--bs-secondary: ' . $settings->second_color . ';';
            }
            if (!empty($settings->color_3)) {
                $style .= '--bs-color-3: ' . $settings->color_3 . ';';
            }
            if (!empty($settings->color_4)) {
                $style .= '--bs-color-4: ' . $settings->color_4 . ';';
            }
            if (!empty($settings->text_color)) {
                $style .= '--bs-body-color: ' . $settings->text_color . ';';
            }
            if (!empty($settings->link_color)) {
                $style .= '--bs-link-color: ' . $settings->link_color . ';';
            }
            if (!empty($settings->link_hover_color)) {
                $style .= '--bs-link-hover-color: ' . $settings->link_hover_color . ';';
            }
            $btn_primary = '';
            if (!empty($settings->btn_bg)) {
                $style .= '--btn_bg: ' . $settings->btn_bg . ';';
                $btn_primary .= '--bs-btn-bg: ' . $settings->btn_bg . ';';
                $btn_primary .= '--bs-btn-border-color: ' . $settings->btn_bg . ';';
            }
            if (!empty($settings->btn_cl)) {
                $style .= '--btn_cl: ' . $settings->btn_cl . ';';
                $btn_primary .= '--bs-btn-color: ' . $settings->btn_cl . ';';
            }
            if (!empty($settings->btn_hv_bg)) {
                $style .= '--btn_hv_bg: ' . $settings->btn_hv_bg . ';';
                $btn_primary .= '--bs-btn-hover-bg: ' . $settings->btn_hv_bg . ';';
                $btn_primary .= '--bs-btn-hover-border-color: ' . $settings->btn_hv_bg . ';';
            }
            if (!empty($settings->btn_hv_cl)) {
                $style .= '--btn_hv_cl: ' . $settings->btn_hv_cl . ';';
                $btn_primary .= '--bs-btn-hover-color: ' . $settings->btn_hv_cl . ';';
            }
            if (!empty($settings->font_size)) {
                $style .= '--bs-body-font-size:' . $settings->font_size . 'px;';
                $style .= 'font-size:' . $settings->font_size . 'px;';
            }
            if (!empty($settings->line_height)) {
                $style .= '--bs-body-line-height:' . $settings->line_height . 'px;';
                $style .= 'line-height:' . $settings->line_height . 'px;';
            }
            if (!empty($settings->font_name)) {
                if (str_contains($settings->font_name, ' ')) {
                    $style .= '--bs-body-font-family: "' . $settings->font_name . '";';
                } else {
                    $style .= '--bs-body-font-family: ' . $settings->font_name . ';';
                }
            }
            $temp = '';
            if (!empty($settings->gutter_x)) {
                $temp .= '--bs-gutter-x:' . (int)$settings->gutter_x . 'px;';
            }
            if (!empty($settings->gutter_y)) {
                $temp .= '--bs-gutter-y:' . (int)$settings->gutter_y . 'px;';
            }
            if ($temp) {
                $style_el .= 'body.yaht-template .row{' . $temp . '}';
            }
            if ($btn_primary) {
                $style_el .= 'body.yaht-template .btn-primary{' . $btn_primary . '}';
            }
        }
        if ($style) {
            $style = 'body.yaht-template{' . $style . '}';
        }
        $footer_style = '';
        if (!empty($settings->footer_bgi)) {
            if (!empty($settings->footer_bgi->status)) {
                $footer_style .= 'background-image: url("' . $settings->footer_bgi->file_url . '/' . $settings->footer_bgi->full_name . '");';
            }
        }
        if (!empty($settings->footer_bgc)) {
            $footer_style .= 'background-color:' . $settings->footer_bgc . ';';
        }
        if (!empty($settings->footer_cl)) {
            $footer_style .= 'color:' . $settings->footer_cl . ';';
        }
        if (!empty($settings->footer_bgz)) {
            $footer_style .= 'background-size: ' . $settings->footer_bgz . ';';
        }
        if (!empty($settings->footer_bgp)) {
            $footer_style .= 'background-position: ' . $settings->footer_bgp . ';';
        }
        if (!empty($settings->footer_bgr)) {
            $footer_style .= 'background-repeat: ' . $settings->footer_bgr . ';';
        }
        if (!empty($settings->footer_bga)) {
            $footer_style .= 'background-attachment: ' . $settings->footer_bga . ';';
        }
        if (!empty($settings->footer_bgm)) {
            $footer_style .= 'background-blend-mode: ' . $settings->footer_bgm . ';';
        }
        if (!empty($settings->footer_bgc)) {
            $footer_style .= 'background-clip: ' . $settings->footer_bgc . ';';
        }
        if (!empty($settings->footer_bgo)) {
            $footer_style .= 'background-origin: ' . $settings->footer_bgo . ';';
        }
        if ($footer_style) {
            $footer_style = '.footer--component{' . $footer_style . '}';
        }
        $header_style = '';
        if (!empty($settings->header_bgi)) {
            if (!empty($settings->header_bgi->status)) {
                $header_style .= 'background-image: url("' . $settings->header_bgi->file_url . '/' . $settings->header_bgi->full_name . '");';
            }
        }
        if (!empty($settings->header_bgc)) {
            $header_style .= 'background-color:' . $settings->header_bgc . ';';
        }
        if (!empty($settings->header_cl)) {
            $header_style .= 'color:' . $settings->header_cl . ';';
        }
        if (!empty($settings->header_bgz)) {
            $header_style .= 'background-size: ' . $settings->header_bgz . ';';
        }
        if (!empty($settings->header_bgp)) {
            $header_style .= 'background-position: ' . $settings->header_bgp . ';';
        }
        if (!empty($settings->header_bgr)) {
            $header_style .= 'background-repeat: ' . $settings->header_bgr . ';';
        }
        if (!empty($settings->header_bga)) {
            $header_style .= 'background-attachment: ' . $settings->header_bga . ';';
        }
        if (!empty($settings->header_bgm)) {
            $header_style .= 'background-blend-mode: ' . $settings->header_bgm . ';';
        }
        if (!empty($settings->header_bgc)) {
            $header_style .= 'background-clip: ' . $settings->header_bgc . ';';
        }
        if (!empty($settings->header_bgo)) {
            $header_style .= 'background-origin: ' . $settings->header_bgo . ';';
        }
        if ($header_style) {
            $header_style = '.header--component{' . $header_style . '}';
        }
        return $style . $style_el . $header_style . $footer_style;
    }
    public static function styleMarginRender($settings): string
    {
        $style = '';
        if (!empty($settings['all'])) {
            $style .= 'margin:' . $settings['all'] . $settings['unit'] . ';';
        } else {
            if ($settings['top'] !== '' && $settings['top'] !== null && $settings['top'] !== false) {
                $style .= 'margin-top:' . $settings['top'] . $settings['unit'] . ';';
            }
            if ($settings['right'] !== '' && $settings['right'] !== null && $settings['right'] !== false) {
                $style .= 'margin-right:' . $settings['right'] . $settings['unit'] . ';';
            }
            if ($settings['bottom'] !== '' && $settings['bottom'] !== null && $settings['bottom'] !== false) {
                $style .= 'margin-bottom:' . $settings['bottom'] . $settings['unit'] . ';';
            }
            if ($settings['left'] !== '' && $settings['left'] !== null && $settings['left'] !== false) {
                $style .= 'margin-left:' . $settings['left'] . $settings['unit'] . ';';
            }
        }
        return $style;
    }
    public static function stylePositionRender($settings): string
    {
        $style = '';
        if ($settings['top'] !== '' && $settings['top'] !== null && $settings['top'] !== false) {
            $style .= 'top:' . $settings['top'] . $settings['unit'] . ';';
        }
        if ($settings['right'] !== '' && $settings['right'] !== null && $settings['right'] !== false) {
            $style .= 'right:' . $settings['right'] . $settings['unit'] . ';';
        }
        if ($settings['bottom'] !== '' && $settings['bottom'] !== null && $settings['bottom'] !== false) {
            $style .= 'bottom:' . $settings['bottom'] . $settings['unit'] . ';';
        }
        if ($settings['left'] !== '' && $settings['left'] !== null && $settings['left'] !== false) {
            $style .= 'left:' . $settings['left'] . $settings['unit'] . ';';
        }
        return $style;
    }
    public static function styleDimensionsRender($settings): string
    {
        $style = '';
        if (isset($settings['width'])) {
            if ($settings['width'] !== '' && $settings['width'] !== null && $settings['width'] !== false) {
                if ($settings['unit'] == 'calc') {
                    $style .= 'width:calc(' . $settings['width'] . ');flex-basis:calc(' . $settings['width'] . ');max-width:calc(' . $settings['width'] . ');';
                } else {
                    $style .= 'width:' . $settings['width'] . $settings['unit'] . ';flex-basis:' . $settings['width'] . $settings['unit'] . ';max-width:' . $settings['width'] . $settings['unit'] . ';';
                }
            }
        }
        if (isset($settings['height'])) {
            if ($settings['height'] !== '' && $settings['height'] !== null && $settings['height'] !== false) {
                if ($settings['unit'] == 'calc') {
                    $style .= 'height:calc(' . $settings['height'] . ');';
                } else {
                    $style .= 'height:' . $settings['height'] . $settings['unit'] . ';';
                }
            }
        }
        return $style;
    }
    public static function stylePaddingRender($settings): string
    {
        $style = '';
        if (!empty($settings['all'])) {
            $style .= 'padding:' . $settings['all'] . $settings['unit'] . ';';
        } else {
            if ($settings['top'] !== '' && $settings['top'] !== null && $settings['top'] !== false) {
                $style .= 'padding-top:' . $settings['top'] . $settings['unit'] . ';';
            }
            if ($settings['right'] !== '' && $settings['right'] !== null && $settings['right'] !== false) {
                $style .= 'padding-right:' . $settings['right'] . $settings['unit'] . ';';
            }
            if ($settings['bottom'] !== '' && $settings['bottom'] !== null && $settings['bottom'] !== false) {
                $style .= 'padding-bottom:' . $settings['bottom'] . $settings['unit'] . ';';
            }
            if ($settings['left'] !== '' && $settings['left'] !== null && $settings['left'] !== false) {
                $style .= 'padding-left:' . $settings['left'] . $settings['unit'] . ';';
            }
        }
        return $style;
    }
    public static function styleDisplayRender($settings): string
    {
        $style = '';
        if ($settings['display'] !== '' && $settings['display'] !== null && $settings['display'] !== false) {
            $style .= 'display:' . $settings['display'] . ';';
        }
        if ($settings['order'] !== '' && $settings['order'] !== null && $settings['order'] !== false) {
            $style .= 'order:' . $settings['order'] . ';';
        }
        return $style;
    }
    public static function styleRadiusRender($settings): string
    {
        $style = '';
        if (!empty($settings['all'])) {
            $style .= 'border-radius:' . $settings['all'] . $settings['unit'] . ';';
        } else {
            if ($settings['top'] !== '' && $settings['top'] !== null && $settings['top'] !== false) {
                $style .= "border-top-left-radius:{$settings['top']}{$settings['unit']};";
            }
            if ($settings['right'] !== '' && $settings['right'] !== null && $settings['right'] !== false) {
                $style .= "border-top-right-radius:{$settings['right']}{$settings['unit']};";
            }
            if ($settings['bottom'] !== '' && $settings['bottom'] !== null && $settings['bottom'] !== false) {
                $style .= "border-bottom-right-radius:{$settings['bottom']}{$settings['unit']};";
            }
            if ($settings['left'] !== '' && $settings['left'] !== null && $settings['left'] !== false) {
                $style .= "border-bottom-left-radius:{$settings['left']}{$settings['unit']};";
            }
        }
        return $style;
    }
    public static function styleBorderRender($settings): string
    {
        $style = '';
        if ($settings['top'] !== '' && $settings['top'] !== null && $settings['top'] !== false) {
            $style .= 'border-top-width:' . $settings['top'] . 'px;';
        }
        if ($settings['right'] !== '' && $settings['right'] !== null && $settings['right'] !== false) {
            $style .= 'border-right-width:' . $settings['right'] . 'px;';
        }
        if ($settings['bottom'] !== '' && $settings['bottom'] !== null && $settings['bottom'] !== false) {
            $style .= 'border-bottom-width:' . $settings['bottom'] . 'px;';
        }
        if ($settings['left'] !== '' && $settings['left'] !== null && $settings['left'] !== false) {
            $style .= 'border-left-width:' . $settings['left'] . 'px;';
        }
        return $style;
    }
    public static function getCss($params, $screen): string
    {
        $css = '';
        if ($screen == 'xs') {
            if (!empty($params['bgi'])) {
                if (!empty($params['bgi']->id)) {
                    $css .= 'background-image:url(' . $params['bgi']->file_url . '/' . $params['bgi']->file_name . '.' . $params['bgi']->file_ext . ');';
                }
            }
            if (!empty($params['bgc']) && $params['bgc'] !== '') {
                $css .= 'background-color:' . $params['bgc'] . ';';
            }
            if (!empty($params['align']) && $params['align'] !== '') {
                $css .= 'text-align:' . $params['align'] . ';';
            }
            if (!empty($params['col']) && $params['col'] !== '') {
                $css .= 'color:' . $params['col'] . ';';
            }
            if (!empty($params['bor'])) {
                if (!empty($params['bor']['type']) && $params['bor']['type'] !== '') {
                    $css .= 'border-style:' . $params['bor']['type'] . ';';
                }
                if ($params['bor']['type'] !== 'none') {
                    if (!empty($params['bor']['color']) && $params['bor']['color'] !== '') {
                        $css .= 'border-color:' . $params['bor']['color'] . ';';
                    }
                    $css .= self::styleBorderRender($params['bor']);
                }
            }
        }
        if (!empty($params['fz'][$screen])) {
            if (!empty($params['fz'][$screen]['size']) && $params['fz'][$screen]['size'] !== '') {
                $css .= 'font-size:' . $params['fz'][$screen]['size'] . 'px;';
            }
        }
        if (!empty($params['dis'][$screen])) {
            $css .= Helpers::styleDisplayRender($params['dis'][$screen]);
        }
        if (!empty($params['mar'][$screen])) {
            $css .= Helpers::styleMarginRender($params['mar'][$screen]);
        }
        if (!empty($params['pad'][$screen])) {
            $css .= Helpers::stylePaddingRender($params['pad'][$screen]);
        }
        if (!empty($params['rad'][$screen])) {
            $css .= Helpers::styleRadiusRender($params['rad'][$screen]);
        }
        if (!empty($params['dim'][$screen])) {
            $css .= Helpers::styleDimensionsRender($params['dim'][$screen]);
        }
        return $css;
    }
    public static function styleBlockRender2($settings, $elID, $type = 'custom', $ex = ''): string
    {
        if (!$elID)
            return '';
        if (is_object($settings)) {
            $settings = (array)$settings;
        }
        $arr = [
            'xs' => [],
            'sm' => [],
            'md' => [],
            'lg' => [],
            'xl' => [],
            'xxl' => [],
        ];
        foreach (self::$responsive as $screen => $value) {
            if (!empty($settings['blk'])) {
                $arr[$screen]['root'] = self::getCss($settings['blk'], $screen);
            } else {
                $arr[$screen]['root'] = '';
            }
            if (!empty($settings['til'])) {
                $arr[$screen]['yaht--block--title'] = self::getCss($settings['til'], $screen);
                $arr[$screen]['yaht--block--title--icon'] = '';
                if (!empty($settings['til']['ifz'])) {
                    if (!empty($settings['til']['ifz'][$screen]['size']) && $settings['til']['ifz'][$screen]['size'] !== '') {
                        $arr[$screen]['yaht--block--title--icon'] .= 'font-size: ' . (int)$settings['til']['ifz'][$screen]['size'] . 'px;';
                        //$arr[$screen]['yaht--block--title--img'] = 'width: '.(int)$settings['til']['ifz'][$screen]['size'].'px;';
                    }
                }
                if (!empty($settings['til']['ipo'])) {
                    $arr[$screen]['yaht--block--title--icon'] .= self::stylePositionRender($settings['til']['ipo'][$screen]);
                }
                if (!empty($settings['til']['idi'])) {
                    $arr[$screen]['yaht--block--title--icon'] .= self::styleDimensionsRender($settings['til']['idi'][$screen]);
                }
            } else {
                $arr[$screen]['yaht--block--title'] = '';
            }
            if (!empty($settings['cot'])) {
                $arr[$screen]['yaht--block--content'] = self::getCss($settings['cot'], $screen);
            } else {
                $arr[$screen]['yaht--block--content'] = '';
            }
            if (!empty($settings['img'])) {
                $arr[$screen]['post-thumbnail'] = '';
                $arr[$screen]['thumb'] = '';
                if ($screen == 'xs') {
                    if (!empty($settings['img']['bor'])) {
                        if (!empty($settings['img']['bor']['type']) && $settings['img']['bor']['type'] !== '') {
                            $arr[$screen]['post-thumbnail'] .= 'border-style:' . $settings['img']['bor']['type'] . ';';
                        }
                        if ($settings['img']['bor']['type'] !== 'none') {
                            if (!empty($settings['img']['bor']['color']) && $settings['img']['bor']['color'] !== '') {
                                $arr[$screen]['post-thumbnail'] .= 'border-color:' . $settings['img']['bor']['color'] . ';';
                            }
                            $arr[$screen]['post-thumbnail'] .= self::styleBorderRender($settings['img']['bor']);
                        }
                    }
                    if (!empty($settings['img']['bgz'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-size: ' . $settings['img']['bgz'] . ';';
                    }
                    if (!empty($settings['img']['bgp'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-position: ' . $settings['img']['bgp'] . ';';
                    }
                    if (!empty($settings['img']['bgr'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-repeat: ' . $settings['img']['bgr'] . ';';
                    }
                    if (!empty($settings['img']['bga'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-attachment: ' . $settings['img']['bga'] . ';';
                    }
                    if (!empty($settings['img']['bgm'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-blend-mode: ' . $settings['img']['bgm'] . ';';
                    }
                    if (!empty($settings['img']['bgc'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-clip: ' . $settings['img']['bgc'] . ';';
                    }
                    if (!empty($settings['img']['bgo'])) {
                        $arr[$screen]['post-thumbnail'] .= 'background-origin: ' . $settings['img']['bgo'] . ';';
                    }
                }
                if (!empty($settings['img']['mar'][$screen])) {
                    $arr[$screen]['post-thumbnail'] .= self::styleMarginRender($settings['img']['mar'][$screen]);
                }
                if (!empty($settings['img']['pad'][$screen])) {
                    $arr[$screen]['post-thumbnail'] .= self::stylePaddingRender($settings['img']['pad'][$screen]);
                }
                if (!empty($settings['img']['rad'][$screen])) {
                    $arr[$screen]['post-thumbnail'] .= self::styleRadiusRender($settings['img']['rad'][$screen]);
                }
                if (!empty($settings['img']['dim'][$screen])) {
                    $arr[$screen]['post-thumbnail'] .= self::styleDimensionsRender($settings['img']['dim'][$screen]);
                    $arr[$screen]['thumb'] .= self::styleDimensionsRender($settings['img']['dim'][$screen]);
                    if ($type == 'slider' || $type == 'banner') {
                        $arr[$screen]['yaht--block--content--item'] = self::styleDimensionsRender($settings['img']['dim'][$screen]);
                    }
                } else {
                    $arr[$screen]['post-thumbnail'] = '';
                    if ($type == 'slider' || $type == 'banner') {
                        $arr[$screen]['yaht--block--content--item'] = '';
                    }
                }
            } else {
                if ($type == 'slider' || $type == 'banner') {
                    $arr[$screen]['yaht--block--content--item'] = '';
                } else {
                    $arr[$screen]['post-thumbnail'] = '';
                }
            }

            /*if (!empty($settings['img']['item_dim'])){
                $arr[$screen]['yaht--block--content--item'] = self::styleDimensionsRender($settings['item_dim'][$screen]);
            }else{
                $arr[$screen]['yaht--block--content--item'] = '';
            }*/

            if ($ex == 'owl') {
                $arr[$screen]['owl-row'] = '';
                $arr[$screen]['owl-col'] = '';
                if (!empty($settings['owl'])) {
                    if (!empty($settings['owl']['row']) && (int)$settings['owl']['row'] > 1) {

                        if (!empty($settings['owl'][$screen]['row_mar']) && $settings['owl'][$screen]['row_mar'] !== '') {
                            $s = (int)$settings['owl'][$screen]['row_mar'] / 2;
                            $arr[$screen]['owl-row'] .= 'margin-top: -' . $s . 'px;padding-bottom:-' . $s . 'px;';
                            $arr[$screen]['owl-col'] .= 'padding-top: ' . $s . 'px;padding-bottom:' . $s . 'px;';
                        }
                    }
                }
            } else if ($ex == 'grid') {
                $arr[$screen]['row'] = '';
                $arr[$screen]['col'] = '';
                if (!empty($settings['grid'])) {
                    if (!empty($settings['grid'][$screen]['col_mar']) && $settings['grid'][$screen]['col_mar'] !== '') {
                        $s = (int)$settings['grid'][$screen]['col_mar'] / 2;
                        $arr[$screen]['row'] .= 'margin-left: -' . $s . 'px;margin-right:-' . $s . 'px;';
                        $arr[$screen]['col'] .= 'padding-left:' . $s . 'px;padding-right:' . $s . 'px;';
                    }
                    if (!empty($settings['grid'][$screen]['row_mar']) && $settings['grid'][$screen]['row_mar'] !== '') {
                        $s = (int)$settings['grid'][$screen]['row_mar'] / 2;
                        $arr[$screen]['row'] .= 'margin-top: -' . $s . 'px;margin-bottom:-' . $s . 'px;';
                        $arr[$screen]['col'] .= 'padding-top:' . $s . 'px;padding-bottom:' . $s . 'px;';
                    }
                }
            }
        }
        $style = '';
        if (!empty($settings['effect_col']) && $settings['effect_col'] !== '') {
            $style .= '#' . $elID . ' .hv-effect:before,#' . $elID . ' .hv-effect:after, #' . $elID . ' .hv-effect span:before, #' . $elID . ' .hv-effect span:after, #' . $elID . ' .hv-effect i{background-color:' . $settings['effect_col'] . '}';
        }
        if (!empty($settings['font_name'])) {
            $style .= '#' . $elID . '{font-family: "' . $settings['font_name'] . '"}';
        }
        foreach ($arr as $screen => $values) {
            if ($values) {
                $css = '';
                foreach ($values as $key => $value) {
                    if ($value) {
                        if ($key == 'root') {
                            $css .= '#' . $elID . '{' . $value . '}';
                        } else {
                            $css .= '#' . $elID . ' .' . $key . '{' . $value . '}';
                        }
                    }
                }
                if ($css) {
                    if ($screen == 'xs') {
                        $style .= $css;
                    } else {
                        $style .= '@media (min-width: ' . self::$responsive[$screen] . 'px){' . $css . '}';
                    }
                }
            }
        }
        return $style;
    }
    public static function styleImageRender($settings, $elID, $imageID): string
    {
        if (!$elID || !$imageID)
            return '';
        if (is_object($settings)) {
            $settings = (array)$settings;
        }
        $css = '';
        if (!empty($settings['bgz'])) {
            $css .= 'background-size: ' . $settings['bgz'] . ';';
        }
        if (!empty($settings['bgp'])) {
            $css .= 'background-position: ' . $settings['bgp'] . ';';
        }
        if (!empty($settings['bgr'])) {
            $css .= 'background-repeat: ' . $settings['bgr'] . ';';
        }
        if (!empty($settings['bga'])) {
            $css .= 'background-attachment: ' . $settings['bga'] . ';';
        }
        if (!empty($settings['bgm'])) {
            $css .= 'background-blend-mode: ' . $settings['bgm'] . ';';
        }
        if (!empty($settings['bgc'])) {
            $css .= 'background-clip: ' . $settings['bgc'] . ';';
        }
        if (!empty($settings['bgo'])) {
            $css .= 'background-origin: ' . $settings['bgo'] . ';';
        }
        if ($css) {
            return '#' . $elID . ' .image_' . $imageID . '{' . $css . '}';
        }
        return '';
    }
    public static function owlBlockRender($settings): array
    {
        if (is_object($settings)) {
            $settings = (array)$settings;
        }
        $owl = [
            'lazyLoad' => true,
            'loop' => false,
            'nav' => false,
            'dots' => false,
            'autoplay' => false,
            'autoplaySpeed' => 1000,
            'items' => 1,
            'margin' => 30,
        ];
        if (!empty($settings['owl']['lazyLoad'])) {
            $owl['lazyLoad'] = (bool)$settings['owl']['lazyLoad'];
        }
        if (!empty($settings['owl']['loop'])) {
            $owl['loop'] = (bool)$settings['owl']['loop'];
        }
        if (!empty($settings['owl']['nav'])) {
            $owl['nav'] = (bool)$settings['owl']['nav'];
        }
        if (!empty($settings['owl']['dots'])) {
            $owl['dots'] = (bool)$settings['owl']['dots'];
        }
        if (!empty($settings['owl']['autoplay'])) {
            $owl['autoplay'] = (bool)$settings['owl']['autoplay'];
        }
        if (!empty($settings['owl']['autoplaySpeed'])) {
            $owl['autoplaySpeed'] = (int)$settings['owl']['autoplaySpeed'];
        }
        if (!empty($settings['owl']['animateOut'])) {
            $owl['animateOut'] = $settings['owl']['animateOut'];
        }
        if (!empty($settings['owl']['animateIn'])) {
            $owl['animateIn'] = $settings['owl']['animateIn'];
        }
        $responsive = [];
        $scr = [];
        if (isset($settings['owl']['xs']['items']) && $settings['owl']['xs']['items'] !== null && $settings['owl']['xs']['items'] !== false) {
            $owl['items'] = (int)$settings['owl']['xs']['items'];
        }
        if (isset($settings['owl']['xs']['margin']) && $settings['owl']['xs']['margin'] !== null && $settings['owl']['xs']['margin'] !== false) {
            $owl['margin'] = (int)$settings['owl']['xs']['margin'];
        }
        if (isset($settings['owl']['sm']['items']) && $settings['owl']['sm']['items'] !== null && $settings['owl']['sm']['items'] !== false) {
            $scr['items'] = (int)$settings['owl']['sm']['items'];
        }
        if (isset($settings['owl']['sm']['margin']) && $settings['owl']['sm']['margin'] !== null && $settings['owl']['sm']['margin'] !== false) {
            $scr['margin'] = (int)$settings['owl']['sm']['margin'];
        }
        if ($scr) {
            $responsive['576'] = $scr;
            $scr = [];
        }
        if (isset($settings['owl']['md']['items']) && $settings['owl']['md']['items'] !== null && $settings['owl']['md']['items'] !== false) {
            $scr['items'] = (int)$settings['owl']['md']['items'];
        }
        if (isset($settings['owl']['md']['margin']) && $settings['owl']['md']['margin'] !== null && $settings['owl']['md']['margin'] !== false) {
            $scr['margin'] = (int)$settings['owl']['md']['margin'];
        }
        if ($scr) {
            $responsive['768'] = $scr;
            $scr = [];
        }
        if (isset($settings['owl']['lg']['items']) && $settings['owl']['lg']['items'] !== null && $settings['owl']['lg']['items'] !== false) {
            $scr['items'] = (int)$settings['owl']['lg']['items'];
        }
        if (isset($settings['owl']['lg']['margin']) && $settings['owl']['lg']['margin'] !== null && $settings['owl']['lg']['margin'] !== false) {
            $scr['margin'] = (int)$settings['owl']['lg']['margin'];
        }
        if ($scr) {
            $responsive['992'] = $scr;
            $scr = [];
        }
        if (isset($settings['owl']['xl']['items']) && $settings['owl']['xl']['items'] !== null && $settings['owl']['xl']['items'] !== false) {
            $scr['items'] = (int)$settings['owl']['xl']['items'];
        }
        if (isset($settings['owl']['xl']['margin']) && $settings['owl']['xl']['margin'] !== null && $settings['owl']['xl']['margin'] !== false) {
            $scr['margin'] = (int)$settings['owl']['xl']['margin'];
        }
        if ($scr) {
            $responsive['1200'] = $scr;
            $scr = [];
        }
        if (isset($settings['owl']['xxl']['items']) && $settings['owl']['xxl']['items'] !== null && $settings['owl']['xxl']['items'] !== false) {
            $scr['items'] = (int)$settings['owl']['xxl']['items'];
        }
        if (isset($settings['owl']['xxl']['margin']) && $settings['owl']['xxl']['margin'] !== null && $settings['owl']['xxl']['margin'] !== false) {
            $scr['margin'] = (int)$settings['owl']['xxl']['margin'];
        }
        if ($scr) {
            $responsive['1400'] = $scr;
        }
        if ($responsive) {
            $owl['responsive'] = $responsive;
        }
        return $owl;
    }
    public static function gridBlockRender($settings): string
    {
        if (is_object($settings)) {
            $settings = (array)$settings;
        }
        $cl = [];
        if (isset($settings['grid']['xs']['items']) && $settings['grid']['xs']['items'] !== null && $settings['grid']['xs']['items'] !== false) {
            $cl[] = 'row-cols-' . (int)$settings['grid']['xs']['items'];
        }
        if (isset($settings['grid']['sm']['items']) && $settings['grid']['sm']['items'] !== null && $settings['grid']['sm']['items'] !== false) {
            $cl[] = 'row-cols-sm-' . (int)$settings['grid']['sm']['items'];
        }
        if (isset($settings['grid']['md']['items']) && $settings['grid']['md']['items'] !== null && $settings['grid']['md']['items'] !== false) {
            $cl[] = 'row-cols-md-' . (int)$settings['grid']['md']['items'];
        }
        if (isset($settings['grid']['lg']['items']) && $settings['grid']['lg']['items'] !== null && $settings['grid']['lg']['items'] !== false) {
            $cl[] = 'row-cols-lg-' . (int)$settings['grid']['lg']['items'];
        }
        if (isset($settings['grid']['xl']['items']) && $settings['grid']['xl']['items'] !== null && $settings['grid']['xl']['items'] !== false) {
            $cl[] = 'row-cols-xl-' . (int)$settings['grid']['xl']['items'];
        }
        if (isset($settings['grid']['xxl']['items']) && $settings['grid']['xxl']['items'] !== null && $settings['grid']['xxl']['items'] !== false) {
            $cl[] = 'row-cols-xxl-' . (int)$settings['grid']['xxl']['items'];
        }
        if ($cl) {
            return implode(' ', $cl);
        }
        return '';
    }
    public static function MauBaoCao3CongKhaiParams(CustomerReport $report, $period = ''): stdClass
    {
        $results = new stdClass();
        $columns = [
            ['text' => 'STT', 'align' => 'center', 'editable' => false, 'datafield' => 'code', 'width' => '5%', 'cellsalign' => 'center'],
            ['text' => 'Nội dung', 'align' => 'left', 'editable' => false, 'datafield' => 'name', 'width' => '20%', 'cellsalign' => 'left',],
        ];
        $width = 5;
        $columngroups = [];
        $columngroups_checked = [];
        $items = CustomerReportStructure::query()->where('taxonomy', 'column')->where('report_id', $report->id)->where('parent_id', 0)->orderBy('ordering')->get();
        if ($items) {
            foreach ($items as $item) {
                self::MauBaoCao3CongKhaiColumns($item, $columns, $columngroups, $columngroups_checked, $width);
            }
        }
        if ($width >= 100) {
            $columns[1]['width'] = "20%";
        } else {
            $columns[1]['width'] = (100 - $width) . "%";
        }
        $results->columns = $columns;
        $results->columngroups = $columngroups;
        $items = CustomerReportStructure::query()->where('taxonomy', 'row')->where('report_id', $report->id)->where('parent_id', 0)->orderBy('ordering')->get();
        $datas = [];
        if ($period) {
            $datas = CustomerReportData::query()->where('report_id', $report->id)->where('period', $period)->get();
        }
        $rows = [];
        if ($items) {
            foreach ($items as $item) {
                self::MauBaoCao3CongKhaiRows($item, $rows, $columns, $datas);
            }
        }
        $results->rows = $rows;
        return $results;
    }
    public static function BaoCao3CongKhai(CustomerReport $report, $period = ''): array
    {
        $results = [];
        $columns = [
            ['text' => 'STT', 'align' => 'center', 'editable' => false, 'datafield' => 'code', 'width' => '5%', 'cellsalign' => 'center'],
            ['text' => 'Nội dung', 'align' => 'left', 'editable' => false, 'datafield' => 'name', 'width' => '20%', 'cellsalign' => 'left',],
        ];
        $width = 5;
        $columngroups = [];
        $columngroups_checked = [];
        $items = CustomerReportStructure::query()->where('taxonomy', 'column')->where('report_id', $report->id)->where('parent_id', 0)->orderBy('ordering')->get();
        if ($items) {
            foreach ($items as $item) {
                self::MauBaoCao3CongKhaiColumns($item, $columns, $columngroups, $columngroups_checked, $width);
            }
        }
        if ($width >= 100) {
            $columns[1]['width'] = "20%";
        } else {
            $columns[1]['width'] = (100 - $width) . "%";
        }
        $results['columns'] = $columns;
        $results['columngroups'] = $columngroups;
        $items = CustomerReportStructure::query()->where('taxonomy', 'row')->where('report_id', $report->id)->where('parent_id', 0)->orderBy('ordering')->get();
        $datas = [];
        if ($period) {
            $datas = CustomerReportData::query()->where('report_id', $report->id)->where('period', $period)->where('row_id', '>', 0)->get();
        }
        $rows = [];
        if ($items) {
            foreach ($items as $item) {
                self::MauBaoCao3CongKhaiRows($item, $rows, $columns, $datas);
            }
        }
        $results['rows'] = $rows;
        return $results;
    }
    private static function MauBaoCao3CongKhaiColumns(CustomerReportStructure $parent, &$columns, &$columngroups, &$columngroups_checked, &$width): void
    {
        $items = CustomerReportStructure::query()->where('taxonomy', 'column')->where('parent_id', $parent->id)->orderBy('ordering')->get();
        if (count($items)) {
            $columngroups_checked[] = $parent->id;
            $columngroups[] = [
                'text' => $parent->name,
                'name' => 'col_' . $parent->id,
                'align' => 'center',
            ];
            foreach ($items as $item) {
                self::MauBaoCao3CongKhaiColumns($item, $columns, $columngroups, $columngroups_checked, $width);
            }
        } else {
            $col = [
                'text' => $parent->name,
                'datafield' => 'col_' . $parent->id,
                'editable' => true,
            ];
            if (in_array($parent->parent_id, $columngroups_checked)) {
                $col['columngroup'] = 'col_' . $parent->parent_id;
            }
            if (!empty($parent->params->width)) {
                $col['width'] = (float)$parent->params->width . '%';
                $width += (float)$parent->params->width;
            }
            if (!empty($parent->params->align)) {
                $col['align'] = $parent->params->align;
            } else {
                $col['align'] = 'left';
            }
            if (!empty($parent->params->cellsalign)) {
                $col['cellsalign'] = $parent->params->cellsalign;
            } else {
                $col['cellsalign'] = 'top';
            }
            $columns[] = $col;
        }
    }
    private static function MauBaoCao3CongKhaiRows(CustomerReportStructure $parent, &$rows, $columns, $datas = []): void
    {
        $row = [];
        if ($datas) {
            foreach ($columns as $column) {
                if ($column['datafield'] == "code") {
                    $row[$column['datafield']] = $parent->code;
                } elseif ($column['datafield'] == "name") {
                    $row[$column['datafield']] = $parent->name;
                } else {
                    $data = $datas->where('row_id', $parent->id)->value('data');
                    if ($data && !empty($data[$column['datafield']])) {
                        $row[$column['datafield']] = $data[$column['datafield']];
                    } else {
                        $row[$column['datafield']] = "";
                    }
                }
            }
        } else {
            foreach ($columns as $column) {
                if ($column['datafield'] == "code") {
                    $row[$column['datafield']] = $parent->code;
                } elseif ($column['datafield'] == "name") {
                    $row[$column['datafield']] = $parent->name;
                } else {
                    $row[$column['datafield']] = "";
                }
            }
        }
        if ($parent->params) {
            $cellclassname = [];
            if (!empty($parent->params->font_weight)) {
                $cellclassname[] = 'font-weight-' . $parent->params->font_weight;
            }
            if (!empty($parent->params->font_style)) {
                $cellclassname[] = 'font-' . $parent->params->font_style;
            }
            if (!empty($parent->params->font_decoration)) {
                $cellclassname[] = 'text-decoration-' . $parent->params->font_decoration;
            }
            if (!empty($parent->params->align)) {
                $cellclassname[] = 'align-' . $parent->params->align;
            } else {
                $cellclassname[] = 'align-left';
            }
            if (!empty($parent->params->cellsalign)) {
                $cellclassname[] = 'cellsalign-' . $parent->params->cellsalign;
            } else {
                $cellclassname[] = 'cellsalign-top';
            }
            if ($cellclassname) {
                $row['cellclassname'] = implode(' ', $cellclassname);
            }
        } else {
            $row['cellclassname'] = '';
        }
        $row['data_type'] = 'text';
        $row['id'] = $parent->id;
        $rows[] = $row;
        $items = CustomerReportStructure::query()->where('taxonomy', 'row')->where('parent_id', $parent->id)->orderBy('ordering')->get();
        if (count($items)) {
            foreach ($items as $item) {
                self::MauBaoCao3CongKhaiRows($item, $rows, $columns, $datas = []);
            }
        }
    }
    public static function updateTags($tags): bool
    {
        global $currentSystem;
        $items = DB::table(CustomerTag::$tableName)->select(['name'])->where('customer_id', $currentSystem->customer_id)->where('system_id', $currentSystem->id)->whereIn('name', $tags)->get()->pluck('name')->toArray();
        $data = [];
        if ($items) {
            foreach ($tags as $tag) {
                if (!in_array($tag, $items)) {
                    $data[] = ['customer_id' => $currentSystem->customer_id, 'system_id' => $currentSystem->id, 'name' => $tag, 'slug' => self::toSlug($tag)];
                }
            }
        } else {
            foreach ($tags as $tag) {
                $data[] = ['customer_id' => $currentSystem->customer_id, 'system_id' => $currentSystem->id, 'name' => $tag, 'slug' => self::toSlug($tag)];
            }
        }
        if ($data) {
            DB::table(CustomerTag::$tableName)->insert($data);
            Cache::forget('__' . $currentSystem->customer_id . '__' . $currentSystem->id . '__tags');
        }
        return true;
    }
    public static function getRawQuery($sql)
    {

        $query = str_replace(array('?'), array('\'%s\''), $sql->toSql());
        $query = vsprintf($query, $sql->getBindings());
        return $query;
    }
    public static function getPeriodTableName($name, $_period = ''): string
    {
        global $period;
        if (!$_period)
            $_period = $period;
        return 'ped' . preg_replace('/[^a-z0-9]/', '', $_period) . '_' . $name;
    }
    public static function getNextPeriod($_period = ''): string
    {
        global $period;
        if (!$_period)
            $_period = $period;
        $_period = explode('-', $_period);
        $_period[0] = (int)$_period[0] + 1;
        $_period[1] = (int)$_period[1] + 1;
        return implode('-', $_period);
    }
    public static function isFunc($function_name): bool
    {
        if (ini_get('safe_mode')) {
            Log::info('safe_mode');
            return false;
        } else {
            $d = ini_get('disable_functions');
            $s = ini_get('suhosin.executor.func.blacklist');
            if ("$d$s") {
                $array = preg_split('/,\s*/', "$d,$s");
                Log::info(json_encode($array));
                if (in_array($function_name, $array)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        return false;
    }
    public static function getYoutubeThumb($url, $type = 'hqdefault.jpg'): bool
    {
        $query_str = parse_url($url, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        if (!empty($query_params['v'])) {
            return "https://i.ytimg.com/vi/" . $query_params['v'] . "/" . $type;
        }
        return '';
    }
    public static function getThumbSize($size = 'thumb')
    {
        global $currentSystem;
        $size = HelperCustomerFile::$thumb_size;
        if (isset($currentSystem->settings->options->thumb_width)) {
            if ((int)$currentSystem->settings->options->thumb_width) {
                $size['width'] = (int)$currentSystem->settings->options->thumb_width;
            } else {
                $size['width'] = null;
            }
        }
        if (isset($currentSystem->settings->options->thumb_height)) {
            if ((int)$currentSystem->settings->options->thumb_height) {
                $size['height'] = (int)$currentSystem->settings->options->thumb_height;
            } else {
                $size['height'] = null;
            }
        }
        return $size;
    }
    public static function getImageSizes($size = ''): array
    {
        global $currentSystem;
        $results = [
            'thumb' => [
                'width' => 480,
                'height' => 270
            ],
            'xs' => [
                'width' => 210,
                'height' => null
            ],
            'md' => [
                'width' => 320,
                'height' => null
            ],
            'lg' => [
                'width' => 680,
                'height' => null
            ],
        ];
        if (!empty($currentSystem->settings)) {
            if (!empty($currentSystem->settings->options)) {
                if ($size) {
                    $w = $size . '_width';
                    $h = $size . '_height';
                    return [
                        'width' => !empty($currentSystem->settings->options->$w) ? $currentSystem->settings->options->$w : $results[$size]['width'],
                        'height' => !empty($currentSystem->settings->options->$h) ? $currentSystem->settings->options->$h : $results[$size]['height']
                    ];
                } else {
                    if (!empty($currentSystem->settings->options->thumb_width)) {
                        $results['thumb']['width'] = $currentSystem->settings->options->thumb_width;
                    }
                    if (!empty($currentSystem->settings->options->thumb_height)) {
                        $results['thumb']['height'] = $currentSystem->settings->options->thumb_height;
                    }
                    if (!empty($currentSystem->settings->options->xs_width)) {
                        $results['xs']['width'] = $currentSystem->settings->options->xs_width;
                    }
                    if (!empty($currentSystem->settings->options->xs_height)) {
                        $results['xs']['height'] = $currentSystem->settings->options->xs_height;
                    }
                    if (!empty($currentSystem->settings->options->md_width)) {
                        $results['md']['width'] = $currentSystem->settings->options->md_width;
                    }
                    if (!empty($currentSystem->settings->options->md_height)) {
                        $results['md']['height'] = $currentSystem->settings->options->md_height;
                    }
                    if (!empty($currentSystem->settings->options->lg_width)) {
                        $results['lg']['width'] = $currentSystem->settings->options->lg_width;
                    }
                    if (!empty($currentSystem->settings->options->lg_height)) {
                        $results['lg']['height'] = $currentSystem->settings->options->lg_height;
                    }
                    return $results;
                }
            }
        }
        if ($size && !empty($results[$size])) {
            return $results[$size];
        }
        return $results;
    }
    /**
     * @todo Chuyển đổi $_FILES array thành dạng dễ xử lý
     * Sử dụng khi upload nhiều file cùng lúc (multiple file input)
     * @param array $files $_FILES array từ request
     * @return array Mảng các file đã được normalize
     */
    public static function getMultipleUploadFiles($files): array
    {
        $results = [];
        if ($files) {
            $keys = array_keys($files);
            $total = count($files['name']);
            for ($i = 0; $i < $total; $i++) {
                $results[$i] = [];
                foreach ($keys as $key) {
                    $results[$i][$key] = $files[$key][$i];
                }
            }
        }
        return $results;
    }
    /**
     * @todo Trả về role/permission string cho Supporter
     * Sử dụng trong middleware, authorization để kiểm tra quyền
     * @return string Format: 'role_or_permission:SUPER_ADMIN|SUPPORTER'
     */
    public static function ropSupporter(): string
    {
        return 'role_or_permission:' . RoleType::SUPER_ADMIN->value . "|" . RoleType::SUPPORTER->value;
    }
    /**
     * @todo Trả về role/permission string cho Admin
     * Sử dụng trong middleware, authorization
     * @return string Format: 'role_or_permission:SUPER_ADMIN|SUPPORTER|ADMIN'
     */
    public static function ropAdmin(): string
    {
        return 'role_or_permission:' . RoleType::SUPER_ADMIN->value . "|" . RoleType::SUPPORTER->value . "|" . RoleType::ADMIN->value;
    }
    public static function ropManager(): string
    {
        return 'role_or_permission:' . RoleType::SUPER_ADMIN->value . "|" . RoleType::SUPPORTER->value . "|" . RoleType::ADMIN->value . "|MANAGER_SYSTEM";
    }
    public static function listWeeks(): array
    {
        $dt = Carbon::now();
        $t = $dt->isoWeeksInYear;
        $year = $dt->yearIso;
        $weeks = [];
        for ($i = 1; $i <= $t; $i++) {
            $dt->setISODate($year, $i);
            $weeks[] = [
                'id' => $i,
                'text' => $dt->startOfWeek()->format('d/m/Y') . ' - ' . $dt->endOfWeek()->format('d/m/Y')
            ];
        }
        return array_values($weeks);
    }

    public static function domFind($dom, $selector)
    {
        try {
            return $dom->find($selector);
        } catch (Exception $error) {
            return [];
        }
    }
    public static function removeBase64($content)
    {
        if ($content) {
            $re = '/src="(data:image\/[^;]+;base64[^"]+)"/';
            preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);
            if ($matches) {
                foreach ($matches as $match) {
                    if (!empty($match[1])) {
                        $content = str_replace($match[1], '', $content);
                    }
                }
            }
        }
        return $content;
    }
    /**
     * @todo Lấy MIME type từ extension
     * Sử dụng để set Content-Type khi download file
     * @param string $ext Extension file (vd: 'pdf', 'jpg', 'docx')
     * @return string MIME type (vd: 'application/pdf', 'image/jpeg')
     */
    public static function fileMime($ext): string
    {
        $mime_types = array(
            'doc' => 'application/msword',
            'dot' => 'application/msword',
            'pdf' => 'application/pdf',
            'xls' => 'application/vnd.ms-excel',
            'xlm' => 'application/vnd.ms-excel',
            'xla' => 'application/vnd.ms-excel',
            'xlc' => 'application/vnd.ms-excel',
            'xlt' => 'application/vnd.ms-excel',
            'xlw' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pps' => 'application/vnd.ms-powerpoint',
            'pot' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            '7z' => 'application/x-7z-compressed',
            'rar' => 'application/x-rar-compressed',
            'zip' => 'application/zip',
            'm4a' => 'audio/mp4',
            'mp4a' => 'audio/mp4',
            'mp2' => 'audio/mpeg',
            'mp2a' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'm2a' => 'audio/mpeg',
            'm3a' => 'audio/mpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'txt' => 'text/plain',
            'text' => 'text/plain',
            'mp4' => 'video/mp4',
            'mp4v' => 'video/mp4',
            'mpg4' => 'video/mp4',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'm1v' => 'video/mpeg',
            'm2v' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie'
        );
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } else {
            return 'application/octet-stream';
        }
    }
    public static function hasPostman(): bool
    {
        return !empty($_SERVER['HTTP_POSTMAN_TOKEN']);
    }
    /**
     * @todo Kiểm tra user hiện tại có phải Supporter/SuperAdmin không
     * Sử dụng để kiểm tra quyền truy cập các chức năng hệ thống
     * @return bool
     */
    public static function isSupporter(): bool
    {
        $user = Auth::user();
        if (!$user || !($user instanceof User))
            return false;
        return $user->hasAnyRole([RoleType::SUPPORTER->value, RoleType::SUPER_ADMIN->value]);
    }
    /**
     * @todo Kiểm tra user hiện tại có phải Admin không
     * Sử dụng để kiểm tra quyền quản lý thư viện
     * @return bool
     */
    public static function isAdmin(): bool
    {
        $user = Auth::user();
        if (!$user || !($user instanceof User))
            return false;
        return $user->hasRole(RoleType::ADMIN->value);
    }
    public static function storeFileData($folder, $file_taxonomy = 'global'): array
    {
        if (!Storage::disk()->exists($folder . '/')) {
            Storage::disk()->makeDirectory($folder . '/');
        }
        $itemData = [
            'folder' => $folder,
            'document_files' => [],
            'must_sign_files' => []
        ];
        if (!empty($_FILES['must_sign_files'])) {
            $must_sign_files = Helpers::getMultipleUploadFiles($_FILES['must_sign_files']);
            if ($must_sign_files) {
                foreach ($must_sign_files as $f) {
                    $file = new FileHelpers($file_taxonomy, $folder, true);
                    $file->uploaderByFile($f, 'OriginalName');
                    if ($file->status) {
                        $itemData['must_sign_files'][] = $file;
                    }
                }
            }
        }
        if (!empty($_FILES['document_files'])) {
            $document_files = Helpers::getMultipleUploadFiles($_FILES['document_files']);
            if ($document_files) {
                foreach ($document_files as $f) {
                    $file = new FileHelpers($file_taxonomy, $folder, true);
                    $file->uploaderByFile($f, 'OriginalName');
                    if ($file->status) {
                        $itemData['document_files'][] = $file;
                    }
                }
            }
        }
        return $itemData;
    }
    /**
     * @todo Cập nhật danh sách file (xóa file cũ, thêm file mới)
     * Sử dụng khi edit form có file upload, cần xóa file không còn được chọn
     * @param array $itemData Data hiện tại có chứa files
     * @param array $inputs Input từ form (chứa danh sách file được giữ lại)
     * @param string $file_taxonomy Loại file
     * @return array Data đã được cập nhật
     */
    /**
     * @todo Kiểm tra user hiện tại có quyền upload file không
     * Chỉ cho phép SUPER_ADMIN, SUPPORTER, ADMIN, LIBRARIAN upload file
     * Sử dụng trong controller/service trước khi cho phép upload
     *
     * Ví dụ sử dụng:
     * if (!Helpers::canUploadFile()) {
     *     return response()->json(['message' => 'Không có quyền upload'], 403);
     * }
     *
     * @return bool
     */
    public static function canUploadFile(): bool
    {
        $user = Auth::user();
        if (!$user || !($user instanceof User)) {
            return false;
        }
        // Cho phép: SUPER_ADMIN, SUPPORTER, ADMIN, LIBRARIAN
        return $user->hasAnyRole([
            RoleType::SUPER_ADMIN->value,
            RoleType::SUPPORTER->value,
            RoleType::ADMIN->value,
            RoleType::LIBRARIAN->value,
        ]);
    }

    /**
     * @todo Kiểm tra user có quyền upload file theo taxonomy cụ thể không
     *
     * Quyền upload:
     * - SUPER_ADMIN, SUPPORTER, ADMIN: Upload mọi loại file
     * - LIBRARIAN: Chỉ upload được: books, book_covers, ebooks, fines, borrows
     * - Các role khác: Không được upload
     *
     * Ví dụ sử dụng:
     * if (!Helpers::canUploadFileByTaxonomy('books')) {
     *     return response()->json(['message' => 'Không có quyền upload ảnh bìa sách'], 403);
     * }
     *
     * @param string $taxonomy Loại file ('books', 'users', 'system', etc.)
     * @return bool
     */
    public static function canUploadFileByTaxonomy(string $taxonomy): bool
    {
        $user = Auth::user();
        if (!$user || !($user instanceof User)) {
            return false;
        }

        // SUPER_ADMIN và SUPPORTER có thể upload mọi loại file
        if ($user->hasAnyRole([RoleType::SUPER_ADMIN->value, RoleType::SUPPORTER->value])) {
            return true;
        }

        // ADMIN có thể upload mọi loại file
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        // LIBRARIAN chỉ có thể upload một số loại file
        if ($user->hasRole(RoleType::LIBRARIAN->value)) {
            $allowedTaxonomies = ['books', 'book_covers', 'ebooks', 'fines', 'borrows'];
            return in_array($taxonomy, $allowedTaxonomies);
        }

        return false;
    }

    public static function updateFileData($itemData, $inputs, $file_taxonomy = 'global'): array
    {
        $newFiles = [];
        if (!empty($inputs['mustsign_files'])) {
            if (!empty($itemData['must_sign_files'])) {
                foreach ($itemData['must_sign_files'] as $file) {
                    if (in_array($file->file_name, $inputs['mustsign_files'])) {
                        $newFiles[] = $file;
                    } else {
                        FileHelpers::deleteAttachment($file);
                    }
                }
            }
        } else {
            if (!empty($itemData['must_sign_files'])) {
                foreach ($itemData['must_sign_files'] as $file) {
                    FileHelpers::deleteAttachment($file);
                }
            }
        }
        if (!empty($_FILES['must_sign_files'])) {
            $files = Helpers::getMultipleUploadFiles($_FILES['must_sign_files']);
            if ($files) {
                foreach ($files as $f) {
                    $file = new FileHelpers($file_taxonomy, $itemData['folder'], true);
                    $file->uploaderByFile($f, 'OriginalName');
                    if ($file->status) {
                        $newFiles[] = $file;
                    }
                }
            }
        }
        $itemData['must_sign_files'] = $newFiles;
        $newFiles = [];
        if (!empty($inputs['documents'])) {
            if (!empty($itemData['document_files'])) {
                foreach ($itemData['document_files'] as $file) {
                    if (in_array($file->file_name, $inputs['documents'])) {
                        $newFiles[] = $file;
                    } else {
                        FileHelpers::deleteAttachment($file);
                    }
                }
            }
        } else {
            if (!empty($itemData['document_files'])) {
                foreach ($itemData['document_files'] as $file) {
                    FileHelpers::deleteAttachment($file);
                }
            }
        }
        if (!empty($_FILES['document_files'])) {
            $files = Helpers::getMultipleUploadFiles($_FILES['document_files']);
            if ($files) {
                foreach ($files as $f) {
                    $file = new FileHelpers($file_taxonomy, $itemData['folder'], true);
                    $file->uploaderByFile($f, 'OriginalName');
                    if ($file->status) {
                        $newFiles[] = $file;
                    }
                }
            }
        }
        $itemData['document_files'] = $newFiles;
        return $itemData;
    }
}
