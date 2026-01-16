<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Lấy danh sách users có lọc và phân trang
     *
     * @param array $filters ['keyword', 'role', 'status', 'roles' (array)]
     * @param int $limit Số lượng mỗi trang
     * @param array $with Các relationships cần eager load
     * @return LengthAwarePaginator
     */
    public function getUsers(array $filters = [], int $limit = 10, array $with = ['roles']): LengthAwarePaginator
    {
        $query = User::query()->with($with);

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_code', 'LIKE', '%' . $keyword . '%');
            });
        }

        if (!empty($filters['role'])) {
            $role = $filters['role'];
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if (!empty($filters['roles']) && is_array($filters['roles'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->whereIn('name', $filters['roles']);
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($limit);
    }

    /**
     * Lấy chi tiết user theo ID kèm relationships
     *
     * @param int|string $id
     * @param array $with Các relationships cần load
     * @return User|null
     */
    public function getUserById($id, array $with = ['roles', 'customer']): ?User
    {
        return User::with($with)->find($id);
    }

    /**
     * Lấy chi tiết user theo ID hoặc throw exception
     *
     * @param int|string $id
     * @param array $with Các relationships cần load
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserByIdOrFail($id, array $with = ['roles', 'customer']): User
    {
        return User::with($with)->findOrFail($id);
    }

    /**
     * Tìm user theo email
     *
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Tìm user theo mã sinh viên/nhân viên
     *
     * @param string $code
     * @return User|null
     */
    public function getUserByCode(string $code): ?User
    {
        return User::where('user_code', $code)->first();
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     *
     * @param string $email
     * @param int|null $excludeUserId ID user cần loại trừ (khi update)
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        return $query->exists();
    }

    /**
     * Kiểm tra user_code đã tồn tại chưa
     *
     * @param string $code
     * @param int|null $excludeUserId ID user cần loại trừ (khi update)
     * @return bool
     */
    public function userCodeExists(string $code, ?int $excludeUserId = null): bool
    {
        $query = User::where('user_code', $code);
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        return $query->exists();
    }

    /**
     * Tạo mới user (Admin tạo)
     * Lưu ý: Validation đã được xử lý ở StoreUserRequest
     *
     * @param array $data ['name', 'email', 'password', 'user_code', 'status', 'role' hoặc 'roles']
     * @return User
     * @throws \Exception
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        try {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (!isset($data['status'])) {
                $data['status'] = 1;
            }

            $user = User::create($data);

            if (isset($data['role'])) {
                $this->assignRole($user->id, $data['role']);
            } elseif (isset($data['roles']) && is_array($data['roles'])) {
                $this->assignRoles($user->id, $data['roles']);
            }

            DB::commit();

            return $user->fresh(['roles']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin user
     *
     * Lưu ý: Validation đã được xử lý ở UpdateUserRequest
     *
     * @param int|string $id
     * @param array $data ['name', 'email', 'password', 'user_code', 'status', 'role' hoặc 'roles']
     * @return User
     * @throws \Exception
     */
    public function updateUser($id, array $data): User
    {
        $user = User::findOrFail($id);

        DB::beginTransaction();
        try {
            // Hash password nếu có (và không rỗng)
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Cập nhật thông tin user
            $user->update($data);

            // Cập nhật role(s)
            if (isset($data['role'])) {
                $role = Role::where('name', $data['role'])->first();
                if ($role) {
                    $user->roles()->sync([$role->id]);
                }
            } elseif (isset($data['roles']) && is_array($data['roles'])) {
                $this->syncRoles($user->id, $data['roles']);
            }

            DB::commit();

            // Refresh để load relationships mới nhất
            return $user->fresh(['roles']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Thay đổi trạng thái (khóa/mở khóa)
     *
     * @param int|string $id
     * @param int $status 0 = inactive/khóa, 1 = active/mở
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function changeStatus($id, int $status): User
    {
        $user = User::findOrFail($id);

        if (!in_array($status, [0, 1])) {
            throw new \InvalidArgumentException('Status phải là 0 hoặc 1.');
        }

        $user->status = $status;
        $user->save();

        return $user->fresh();
    }

    /**
     * Khóa tài khoản user
     *
     * @param int|string $id
     * @return User
     */
    public function lockUser($id): User
    {
        return $this->changeStatus($id, 0);
    }

    /**
     * Mở khóa tài khoản user
     *
     * @param int|string $id
     * @return User
     */
    public function unlockUser($id): User
    {
        return $this->changeStatus($id, 1);
    }

    /**
     * Xóa user (soft delete)
     *
     * @param int|string $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteUser($id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    /**
     * Xóa vĩnh viễn user (force delete)
     *
     * @param int|string $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function forceDeleteUser($id): bool
    {
        $user = User::withTrashed()->findOrFail($id);
        return $user->forceDelete();
    }

    /**
     * Khôi phục user đã bị xóa
     *
     * @param int|string $id
     * @return bool
     */
    public function restoreUser($id): bool
    {
        $user = User::withTrashed()->find($id);
        if ($user && $user->trashed()) {
            return $user->restore();
        }
        return false;
    }

    /**
     * Gán một vai trò cho user
     *
     * @param int|string $userId
     * @param string $roleName
     * @return bool
     */
    public function assignRole($userId, string $roleName): bool
    {
        $user = User::find($userId);
        $role = Role::where('name', $roleName)->first();

        if (!$user || !$role) {
            return false;
        }

        if (!$user->roles()->where('name', $roleName)->exists()) {
            $user->roles()->attach($role->id);
            return true;
        }

        return false;
    }

    /**
     * Gán nhiều vai trò cho user
     *
     * @param int|string $userId
     * @param array $roleNames
     * @return bool
     */
    public function assignRoles($userId, array $roleNames): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $roles = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        if (empty($roles)) {
            return false;
        }

        foreach ($roles as $roleId) {
            if (!$user->roles()->where('id', $roleId)->exists()) {
                $user->roles()->attach($roleId);
            }
        }

        return true;
    }

    /**
     * Đồng bộ roles cho user
     *
     * @param int|string $userId
     * @param array $roleNames
     * @return bool
     */
    public function syncRoles($userId, array $roleNames): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $roles = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $user->roles()->sync($roles);

        return true;
    }

    /**
     * Loại bỏ một vai trò khỏi user
     *
     * @param int|string $userId
     * @param string $roleName
     * @return bool
     */
    public function removeRole($userId, string $roleName): bool
    {
        $user = User::find($userId);
        $role = Role::where('name', $roleName)->first();

        if (!$user || !$role) {
            return false;
        }

        $user->roles()->detach($role->id);
        return true;
    }

    /**
     * Loại bỏ nhiều vai trò khỏi user
     *
     * @param int|string $userId
     * @param array $roleNames
     * @return bool
     */
    public function removeRoles($userId, array $roleNames): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $roles = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        if (!empty($roles)) {
            $user->roles()->detach($roles);
        }

        return true;
    }

    /**
     * Kiểm tra user có vai trò cụ thể không
     *
     * @param int|string $userId
     * @param string $roleName
     * @return bool
     */
    public function userHasRole($userId, string $roleName): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        return $user->hasRole($roleName);
    }

    /**
     * Kiểm tra user có bất kỳ vai trò nào trong danh sách không
     *
     * @param int|string $userId
     * @param array $roleNames
     * @return bool
     */
    public function userHasAnyRole($userId, array $roleNames): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        return $user->hasAnyRole($roleNames);
    }

    /**
     * Kiểm tra user có permission không
     *
     * @param int|string $userId
     * @param string $permissionName
     * @return bool
     */
    public function userHasPermission($userId, string $permissionName): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        return $user->hasPermission($permissionName);
    }

    /**
     * Đổi mật khẩu cho user
     *
     * @param int|string $id
     * @param string $newPassword
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function changePassword($id, string $newPassword): User
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make($newPassword);
        $user->save();

        return $user->fresh();
    }

    /**
     * Lấy danh sách users theo role
     *
     * @param string $roleName
     * @param array $with Relationships cần load
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole(string $roleName, array $with = ['roles'])
    {
        return User::whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        })->with($with)->get();
    }

    /**
     * Đếm số lượng users
     *
     * @param array $filters
     * @return int
     */
    public function countUsers(array $filters = []): int
    {
        $query = User::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        return $query->count();
    }

    /**
     * Lấy users đã bị xóa (soft deleted)
     *
     * @param array $filters
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function getDeletedUsers(array $filters = [], int $limit = 10): LengthAwarePaginator
    {
        $query = User::onlyTrashed()->with('roles');

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_code', 'LIKE', '%' . $keyword . '%');
            });
        }

        return $query->orderByDesc('deleted_at')->paginate($limit);
    }
}
