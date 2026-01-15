<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    public array $arrParams = [];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
    public function getArrParams(): array
    {
        if (!empty($this->params)) {
            $this->arrParams = (array)$this->params;
        }
        return $this->arrParams;
    }
    public function addParam($key, $values): static
    {
        if (!$this->arrParams) {
            $this->arrParams = (array)$this->params;
        }
        $this->arrParams[$key] = $values;
        return $this;
    }
    public function updateParam($key, $value): static
    {
        if (!$this->arrParams) {
            $this->arrParams = (array)$this->params;
        }
        if ($this->arrParams) {
            if (!empty($this->arrParams[$key]) && $this->arrParams[$key]) {
                $this->arrParams[$key] = (array)$this->arrParams[$key];
                if (is_array($value)) {
                    $this->arrParams[$key] = array_values(Helpers::ArrMerge($this->arrParams[$key], $value));
                } else {
                    if (is_numeric($value)) {
                        if (!in_array($value, $this->arrParams[$key])) {
                            $this->arrParams[$key][] = (int)$value;
                        }
                    } else {
                        if (!in_array($value, $this->arrParams[$key])) {
                            $this->arrParams[$key][] = (string)$value;
                        }
                    }
                }
            } else {
                if (is_array($value)) {
                    $this->arrParams[$key] = $value;
                } else {
                    if (is_numeric($value)) {
                        $this->arrParams[$key] = [(int)$value];
                    } else {
                        $this->arrParams[$key] = [(string)$value];
                    }
                }
            }
        } else {
            if (is_array($value)) {
                $this->arrParams[$key] = $value;
            } else {
                if (is_numeric($value)) {
                    $this->arrParams[$key] = [(int)$value];
                } else {
                    $this->arrParams[$key] = [(string)$value];
                }
            }
        }
        return $this;
    }

    public function removeParam($key, $value = ''): static
    {
        if (!$this->arrParams) {
            $this->arrParams = (array)$this->params;
        }
        if ($this->arrParams) {
            if (!empty($this->arrParams[$key])) {
                if (!empty($value) && is_array($this->arrParams[$key])) {
                    if (is_array($value)) {
                        if ($v = array_diff($this->arrParams[$key], $value)) {
                            $this->arrParams[$key] = array_values($v);
                        } else {
                            $this->arrParams[$key] = [];
                        }
                    } else {
                        if (is_numeric($value)) {
                            $value = (int)$value;
                        }
                        if (in_array($value, $this->arrParams[$key])) {
                            if ($v = array_diff($this->arrParams[$key], [$value])) {
                                $this->arrParams[$key] = array_values($v);
                            } else {
                                $this->arrParams[$key] = [];
                            }
                        }
                    }
                } else {
                    unset($this->arrParams[$key]);
                }
            }
        }
        return $this;
    }

    public function increaseParamValue($key, int $value = 0): static
    {
        if (!$this->arrParams && $this->params) {
            $this->arrParams = (array)$this->params;
        }
        if (isset($this->arrParams[$key])) {
            $this->arrParams[$key] += $value;
        } else {
            $this->arrParams[$key] = $value > 0 ? $value : 0;
        }
        return $this;
    }

    public function toParams(): static
    {
        if (!empty($this->arrParams)) {
            $this->params = (object)$this->arrParams;
        } else {
            $this->params = (object)[];
        }
        return $this;
    }
}
