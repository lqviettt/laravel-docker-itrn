<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $employeeId = $this->route('employee') ? $this->route('employee')->id : null;
        $codeRule = $this->isMethod('POST')
            ? 'nullable|string|max:20'
            : [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'code')->ignore($employeeId)
            ];
        return [
            "code" => $codeRule,
            "user_id" => [
                'required',
                'exists:users,id',
                Rule::unique('employees')->ignore($employeeId)
            ],
            'name' => 'required|string|max:32',
            'phone' => 'required|string|max:10',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'start_date' => 'required|date',
            'orders_sold' => 'required|',
        ];
    }

    public function newEmployee()
    {
        $nameParts = explode(' ', $this->name);
        $firstname = array_pop($nameParts);
        $lastname = implode(' ', $nameParts);

        return [
            'code' => Str::random(10),
            "user_id" => $this->user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'start_date' => $this->start_date,
            'orders_sold' => $this->orders_sold,
        ];
    }

    public function updateEmployee()
    {
        $nameParts = explode(' ', $this->name);
        $firstname = array_pop($nameParts);
        $lastname = implode(' ', $nameParts);

        return [
            'code' => $this->code,
            "user_id" => $this->user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'start_date' => $this->start_date,
            'orders_sold' => $this->orders_sold,
        ];
    }
}
