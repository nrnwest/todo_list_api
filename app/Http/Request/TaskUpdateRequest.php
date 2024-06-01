<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Enum\Status;
use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'priority' => (int)$this->priority,
        ]);
    }

    public function rules()
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|integer|between:1,5',
            'status'      => sprintf('required|string|in:%s,%s', Status::TODO->value, Status::DONE->value),
        ];
    }
}
