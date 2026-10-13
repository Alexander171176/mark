<?php

namespace App\Http\Requests\Admin\System\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Проверка полномочий пользователя.
     */
    public function authorize(): bool
    {
        $ability = $this->isMethod('POST')
            ? 'create-roles'
            : 'update-roles';

        return $this->user()?->can($ability) ?? false;
    }

    /**
     * Правила валидации роли.
     */
    public function rules(): array
    {
        $rolesTable = config('permission.table_names.roles', 'roles');
        $permissionsTable = config('permission.table_names.permissions', 'permissions');

        $role = $this->route('role');

        $roleId = is_object($role)
            ? $role->getKey()
            : $role;

        return [
            'name' => [
                'required',
                'string',
                'max:125',

                Rule::unique($rolesTable, 'name')
                    ->where('guard_name', 'sanctum')
                    ->ignore($roleId),
            ],

            'guard_name' => [
                'required',
                Rule::in(['sanctum']),
            ],

            'permissions' => [
                'sometimes',
                'array',
            ],

            'permissions.*' => [
                'required',
                'array',
            ],

            'permissions.*.id' => [
                'required',
                'integer',
                'distinct',

                Rule::exists($permissionsTable, 'id')
                    ->where('guard_name', 'sanctum'),
            ],
        ];
    }

    /**
     * Пользовательские сообщения об ошибках.
     */
    public function messages(): array
    {
        return Lang::get('admin/requests');
    }

    /**
     * Подготовка данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'guard_name' => 'sanctum',
        ]);
    }
}
