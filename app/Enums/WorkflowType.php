<?php

namespace App\Enums;

enum WorkflowType: int
{
  case XU_LY_TAI_LIEU = 1;
  case BAO_CAO = 2;
  case KHAC = 99;

  public static function getComment(): string
  {
    return "1: Xử lý tài liệu; 2: Báo cáo; 99: Khác";
  }
}
