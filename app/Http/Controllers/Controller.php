<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Controller
{
    use AuthorizesRequests, ValidatesRequests;
    protected function jsonResponse($data, $code = 200) : JsonResponse
    {
        global $__token;
        if (!empty($__token)) {
            $data['__token'] = $__token;
        }
        return response()->json($data, $code, ['Content-Type'=>'application/json;charset=UTF-8','charset'=>'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    protected function jsonResponseHtml($html, $code = 200) {
        return response($html, $code, ['Content-Type'=>'text/html;charset=UTF-8','charset'=>'utf-8']);
    }

    protected function init() {
        global $period;
        foreach (['plans', 'comments', 'notes', 'reports'] as $name) {
            $tableName = Helpers::getPeriodTableName($name, $period);
            Cache::forget('__table__'.$tableName);
            if (!Cache::has('__table__'.$tableName)) {
                if (!Schema::hasTable($tableName)) {
                    $this->createTable($name, $tableName);
                } else {
                    Cache::forever('__table__'.$tableName, $tableName,1);
                }
            }
        }
    }
}
