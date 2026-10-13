<?php

namespace App\Http\Requests\Admin\System\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    /**
     * Проверка полномочий пользователя.
     */
    public function authorize(): bool
    {
        $ability = $this->isMethod('POST')
            ? 'create-permissions'
            : 'update-permissions';

        return $this->user()?->can($ability) ?? false;
    }

    /**
     * Правила валидации разрешения.
     */
    public function rules(): array
    {
        $permissionsTable = config(
            'permission.table_names.permissions',
            'permissions'
        );

        $permission = $this->route('permission');

        $permissionId = is_object($permission)
            ? $permission->getKey()
            : $permission;

        return [
            'name' => [
                'required',
                'string',
                'max:125',

                Rule::unique($permissionsTable, 'name')
                    ->where('guard_name', 'sanctum')
                    ->ignore($permissionId),
            ],

            'guard_name' => [
                'required',
                Rule::in(['sanctum']),
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
