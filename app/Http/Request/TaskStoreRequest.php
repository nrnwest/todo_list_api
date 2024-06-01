<?php

declare(strict_types=1);

namespace App\Http\Request;

use App\Enum\Status;
use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    protected function prepareForValidation()
    {
        $parent_id = (int)$this->parent_id;
        $this->merge([
            'priority'  => (int)$this->priority,
            'parent_id' => $parent_id === 0 ? null : $parent_id,
        ]);
    }

    public function rules()
    {
        return [
            'parent_id'   => 'nullable|integer|exists:tasks,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|integer|between:1,5',
            'status'      => sprintf('required|string|in:%s,%s', Status::TODO->value, Status::DONE->value),
        ];
    }
}
