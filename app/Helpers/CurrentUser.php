<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class CurrentUser
{
    public int $id = 0;
    public string $name = '';
    public string $user_code = '';
    public int $is_admin = 0;
    public bool $use_hns_sign = false;
    public array $roles = [];
    public array $permissions = [];
    public array $params = [];
    /**
     * @todo Khởi tạo CurrentUser từ User model
     * Lưu thông tin user, roles, permissions để sử dụng trong session
     * @param \App\Models\User $user User model từ database
     */
    public function __construct($user)
    {
        $this->id = $user->id;
        $this->name = !empty($user->name) ? $user->name : $user->email;
        $this->user_code = $user->user_code;
        $this->is_admin = $user->is_admin ?? 0;
        $this->use_hns_sign = $user->use_hns_sign ?? false;

        $this->roles = [];
        if (!empty($user->roles)) {
            if ($user->roles instanceof \Illuminate\Support\Collection) {
                $this->roles = $user->roles->pluck('name')->toArray();
            } elseif (is_array($user->roles)) {
                $this->roles = array_map(function ($r) {
                    return is_object($r) ? $r->name : $r;
                }, $user->roles);
            }
        }

        $this->permissions = [];
        if (!empty($user->permissions)) {
            if ($user->permissions instanceof \Illuminate\Support\Collection) {
                $this->permissions = $user->permissions->pluck('name')->toArray();
            } elseif (is_array($user->permissions)) {
                $this->permissions = array_map(function ($p) {
                    return is_object($p) ? $p->name : $p;
                }, $user->permissions);
            }
        }

        $this->params = (array)($user->params ?? []);
    }

    /**
     * @todo Kiểm tra user có role hoặc permission nào trong danh sách không
     * Sử dụng trong middleware, authorization (format: "ADMIN|LIBRARIAN" hoặc "VIEW_BOOKS|EDIT_BOOKS")
     * @param string $rolesOrPermission Danh sách roles/permissions phân cách bởi "|"
     * @return bool
     */
    public function hasRoleOrPermission(string $rolesOrPermission): bool
    {
        if ((!empty($this->roles) || !empty($this->permissions)) && $rolesOrPermission) {
            $rolesOrPermission = explode("|", $rolesOrPermission);
            if (!empty($this->roles)) {
                foreach ($this->roles as $role) {
                    if (in_array($role, $rolesOrPermission)) {
                        return true;
                    }
                }
            }
            if (!empty($this->permissions)) {
                foreach ($this->permissions as $permission) {
                    if (in_array($permission, $rolesOrPermission)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @todo Kiểm tra user có role cụ thể không
     * Sử dụng để kiểm tra quyền theo role (ADMIN, LIBRARIAN, STUDENT, LECTURER)
     * @param string $role Tên role hoặc danh sách roles phân cách bởi "|"
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        if (!empty($this->roles) && $role) {
            $roles = explode("|", $role);
            foreach ($this->roles as $r) {
                if (in_array($r, $roles)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @todo Kiểm tra user có permission cụ thể không
     * Sử dụng để kiểm tra quyền chi tiết (VIEW_BOOKS, EDIT_BOOKS, DELETE_BOOKS, etc.)
     * @param string $permission Tên permission hoặc danh sách permissions phân cách bởi "|"
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        if (!empty($this->permissions) && $permission) {
            $permissions = explode("|", $permission);
            foreach ($this->permissions as $p) {
                if (in_array($p, $permissions)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @todo Kiểm tra user có phải Admin không (is_admin = 1)
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin == 1;
    }

    /**
     * @todo Kiểm tra user có phải Super Admin không (is_admin = 9)
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        if ($this->is_admin == 9) {
            return true;
        }
        return false;
    }
    /**
     * @todo Kiểm tra user có phải Supporter không (is_admin = 8 hoặc 9)
     * @return bool
     */
    public function isSupporter()
    {
        if ($this->is_admin == 8 || $this->is_admin == 9) {
            return true;
        }
        return false;
    }
}
