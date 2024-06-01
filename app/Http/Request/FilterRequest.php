<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Enum\Order;
use App\Enum\Status;
use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'created_at'   => $this->input('created_at', Order::ASC->value),
            'completed_at' => $this->input('completed_at', Order::ASC->value),
        ]);
    }

    public function rules()
    {
        return [
            'search'       => 'string',
            'priority'     => 'integer|between:1,5',
            'status'       => sprintf('string|in:%s,%s', Status::TODO->value, Status::DONE->value),
            'created_at'   => sprintf('string|in:%s,%s', Order::ASC->value, Order::DESC->value),
            'completed_at' => sprintf('string|in:%s,%s', Order::ASC->value, Order::DESC->value),
        ];
    }
}
