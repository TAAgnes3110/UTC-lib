<?php

namespace App\Enums;

enum RoleType: string
{
    case SUPER_ADMIN = "SUPER_ADMIN";
    case SUPPORTER = "SUPPORTER";
    case ADMIN = "ADMIN";
    case LIBRARIAN = "LIBRARIAN";
    case LECTURER = "LECTURER";
    case MEMBER = "MEMBER";
    case STUDENT = "STUDENT";
    case USER = "USER";

    /**
     * @param int|string $value
     * @return array|string|null
     * @todo Lấy tên hiển thị (label) từ giá trị integer
     */
    public static function getName(int|string $value): array|string|null
    {
        $result = collect(self::cases())->where('value', $value)->first();
        if (!$result) {
            return null;
        }
        return __('enums.RoleType' . $result->name);
    }

    /**
     * @return array|string|null
     * @todo Trả về mảng key-value đầy đủ thông tin
     * Dùng khi tra cứu tên biến
     */
    public static function getNames(): array|string|null
    {
        return collect(self::cases())->mapWithKeys(function ($it) {
            return [$it->name => [
                'value' => $it->value,
                'label' => __('enums.RoleType.' . $it->name),
            ]];
        })->toArray();
    }

    /**
     * @return array|string|null
     * @todo Trả về mảng dùng cho Dropdown/Select ở FE
     */
    public static function getRoleTypes(): array|string|null
    {
        return collect(self::cases())->map(function ($it) {
            return [
                'id' => $it->value,
                'text' => __('enums.RoleType.' . $it->name),
            ];
        })->toArray();
    }

    /**
     * @return array
     * @todo Lấy danh sách chỉ gồm các giá trị (ID)
     */
    public static function values(): array
    {
        return collect(self::cases())->mapWithKeys(function ($it) {
            return $it->value;
        })->toArray();
    }

    /**
     * @return string
     * @todo Tạo chuỗi comment cho Database Migration
     */
    public static function getComment(): string
    {
        return collect(self::cases())->mapWithKeys(function ($it) {
            return $it->value . ':' . __('enums.RoleType.' . $it->name);
        })->join(', ');
    }
}
