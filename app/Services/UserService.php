<?php

namespace App\Services;

class UserService
{
  /**
   * Lấy danh sách users có lọc và phân trang
   * @param array $filters ['keyword', 'role', 'status']
   * @param int $limit
   */
  public function getUsers(array $filters = [], $limit = 10) {}

  /**
   * Lấy chi tiết user theo ID
   */
  public function getUserById($id) {}

  /**
   * Tìm user theo email
   */
  public function getUserByEmail($email) {}

  /**
   * Tìm user theo mã sinh viên/nhân viên
   */
  public function getUserByCode($code) {}

  /**
   * Tạo mới user (Admin tạo)
   * @param array $data
   */
  public function createUser(array $data) {}

  /**
   * Cập nhật thông tin user
   */
  public function updateUser($id, array $data) {}

  /**
   * Thay đổi trạng thái (khóa/mở khóa)
   */
  public function changeStatus($id, $status) {}

  /**
   * Xóa user
   */
  public function deleteUser($id) {}

  /**
   * Gán vai trò cho user
   */
  public function assignRole($userId, $roleName) {}

  /**
   * Loại bỏ vai trò
   */
  public function removeRole($userId, $roleName) {}
}
