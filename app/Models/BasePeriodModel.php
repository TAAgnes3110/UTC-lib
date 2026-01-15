<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasePeriodModel extends Model
{
    use HasFactory;
    protected string $table_prefix = '';
    public array $arrParams = [];
    public function __construct(array $attributes = [])
    {
        global $period;
        parent::__construct($attributes);
        if ($period){
            $this->table_prefix = 'ped'.preg_replace('/[^a-z0-9]/', '', $period).'_';
        }
    }
    public function getTable()
    {
        $table = parent::getTable();

        if ($this->table_prefix && !str_starts_with($table, $this->table_prefix)) {
            return $this->table_prefix . $table;
        }
        return $table;
    }
    public function setPeriod(string $period): static
    {
        $this->table_prefix = 'ped' . preg_replace('/[^a-z0-9]/', '', $period) . '_';
        return $this;
    }
    public static function forPeriod(string $period)
    {
        $instance = new static();
        $instance->setPeriod($period);
        return $instance->newQuery();
    }
    public function addParam($key, $values): static
    {
        if (!$this->arrParams){
            $this->arrParams = (array)$this->params;
        }
        $this->arrParams[$key] = $values;
        return $this;
    }
    public function updateParam($key, $value): static
    {
        if (!$this->arrParams){
            $this->arrParams = (array)$this->params;
        }
        if ($this->arrParams){
            if (!empty($this->arrParams[$key]) && $this->arrParams[$key]){
                $this->arrParams[$key] = (array)$this->arrParams[$key];
                if (is_array($value)){
                    $this->arrParams[$key] = array_values(Helpers::ArrMerge($this->arrParams[$key], $value));
                }else{
                    if (is_numeric($value)){
                        if (!in_array($value, $this->arrParams[$key])){
                            $this->arrParams[$key][] = (int)$value;
                        }
                    }else{
                        if (!in_array($value, $this->arrParams[$key])){
                            $this->arrParams[$key][] = (string)$value;
                        }
                    }
                }
            }else{
                if (is_array($value)){
                    $this->arrParams[$key] = $value;
                }else{
                    if (is_numeric($value)){
                        $this->arrParams[$key] = [(int)$value];
                    }else{
                        $this->arrParams[$key] = [(string)$value];
                    }
                }
            }
        }else{
            if (is_array($value)){
                $this->arrParams[$key] = $value;
            }else{
                if (is_numeric($value)){
                    $this->arrParams[$key] = [(int)$value];
                }else{
                    $this->arrParams[$key] = [(string)$value];
                }
            }
        }
        return $this;
    }
    public function removeParam($key, $value=''): static
    {
        if (!$this->arrParams){
            $this->arrParams = (array)$this->params;
        }
        if ($this->arrParams){
            if (!empty($this->arrParams[$key])){
                if ($value && is_array($this->arrParams[$key])){
                    if (is_array($value)){
                        if ($v = array_diff($this->arrParams[$key], [$value])){
                            $this->arrParams[$key] = array_values($v);
                        }else{
                            $this->arrParams[$key] = [];
                        }
                    }else{
                        if (is_numeric($value)){
                            $value = (int)$value;
                        }
                        if (in_array($value, $this->arrParams[$key])){
                            if ($v = array_diff($this->arrParams[$key], [$value])){
                                $this->arrParams[$key] = array_values($v);
                            }else{
                                $this->arrParams[$key] = [];
                            }
                        }
                    }
                }else{
                    unset($this->arrParams[$key]);
                }
            }
        }
        return $this;
    }
    public function toParams(): static
    {
        if (!empty($this->arrParams)){
            $this->params = (object)$this->arrParams;
        }else{
            $this->params = (object)[];
        }
        return $this;
    }
}
