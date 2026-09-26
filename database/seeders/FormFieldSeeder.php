<?php

namespace Database\Seeders;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormField\FormField;
use Illuminate\Database\Seeder;
use RuntimeException;

class FormFieldSeeder extends Seeder
{
    /**
     * Заполнение полей системных форм приложения.
     */
    public function run(): void
    {
        $forms = [

            /*
            |--------------------------------------------------------------------------
            | Консультация
            |--------------------------------------------------------------------------
            */

            'consultation' => [
                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваше имя',
                            'placeholder' => 'Введите ваше имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Атыңыз',
                            'placeholder' => 'Атыңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your name',
                            'placeholder' => 'Enter your name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'email',
                        'type' => 'email',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => [
                            'email',
                            'max:255',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'message',
                        'type' => 'textarea',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 400,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:2000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'rows' => 5,
                            'maxlength' => 2000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваш вопрос',
                            'placeholder' => 'Расскажите, чем мы можем вам помочь',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Сұрағыңыз',
                            'placeholder' => 'Сізге қалай көмектесе алатынымызды жазыңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your question',
                            'placeholder' => 'Tell us how we can help you',
                            'description' => null,
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Подбор оборудования
            |--------------------------------------------------------------------------
            */

            'selection' => [
                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваше имя',
                            'placeholder' => 'Введите ваше имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Атыңыз',
                            'placeholder' => 'Атыңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your name',
                            'placeholder' => 'Enter your name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'equipment_type',
                        'type' => 'select',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => null,
                        'width' => '1/2',
                        'settings' => null,
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Тип оборудования',
                            'placeholder' => 'Выберите тип оборудования',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Жабдық түрі',
                            'placeholder' => 'Жабдық түрін таңдаңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Equipment type',
                            'placeholder' => 'Select equipment type',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'object_type',
                        'type' => 'select',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 400,
                        'default_value' => null,
                        'validation' => null,
                        'width' => '1/2',
                        'settings' => null,
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Тип объекта',
                            'placeholder' => 'Выберите тип объекта',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Нысан түрі',
                            'placeholder' => 'Нысан түрін таңдаңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Facility type',
                            'placeholder' => 'Select facility type',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'city',
                        'type' => 'text',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 500,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Город',
                            'placeholder' => 'Укажите город',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Қала',
                            'placeholder' => 'Қаланы көрсетіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'City',
                            'placeholder' => 'Enter city',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'message',
                        'type' => 'textarea',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 600,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:3000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'rows' => 5,
                            'maxlength' => 3000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Параметры и комментарий',
                            'placeholder' => 'Укажите необходимые параметры оборудования или особенности объекта',
                            'description' => 'Чем подробнее информация, тем точнее мы сможем подобрать оборудование.',
                        ],
                        'kk' => [
                            'label' => 'Параметрлер мен түсініктеме',
                            'placeholder' => 'Қажетті жабдық параметрлерін немесе нысан ерекшеліктерін көрсетіңіз',
                            'description' => 'Ақпарат неғұрлым толық болса, жабдықты соғұрлым дәл таңдай аламыз.',
                        ],
                        'en' => [
                            'label' => 'Parameters and comments',
                            'placeholder' => 'Specify equipment requirements or project details',
                            'description' => 'The more details you provide, the more accurately we can select the equipment.',
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Спецификация
            |--------------------------------------------------------------------------
            */

            'specification' => [
                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваше имя',
                            'placeholder' => 'Введите ваше имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Атыңыз',
                            'placeholder' => 'Атыңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your name',
                            'placeholder' => 'Enter your name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'company',
                        'type' => 'text',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Компания',
                            'placeholder' => 'Название компании',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Компания',
                            'placeholder' => 'Компания атауы',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Company',
                            'placeholder' => 'Company name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'email',
                        'type' => 'email',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 400,
                        'default_value' => null,
                        'validation' => [
                            'email',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'files',
                        'type' => 'file',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 500,
                        'default_value' => null,
                        'validation' => null,
                        'width' => 'full',
                        'settings' => [
                            'multiple' => true,
                            'max_files' => 5,
                            'max_size' => 10485760,
                            'extensions' => [
                                'pdf',
                                'doc',
                                'docx',
                                'xls',
                                'xlsx',
                                'csv',
                                'jpg',
                                'jpeg',
                                'png',
                            ],
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Спецификация или техническое задание',
                            'placeholder' => null,
                            'description' => 'Прикрепите до 5 файлов. Максимальный размер одного файла — 10 МБ.',
                        ],
                        'kk' => [
                            'label' => 'Спецификация немесе техникалық тапсырма',
                            'placeholder' => null,
                            'description' => '5 файлға дейін тіркеңіз. Бір файлдың максималды көлемі — 10 МБ.',
                        ],
                        'en' => [
                            'label' => 'Specification or technical requirements',
                            'placeholder' => null,
                            'description' => 'Attach up to 5 files. Maximum size per file is 10 MB.',
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'message',
                        'type' => 'textarea',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 600,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:3000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'rows' => 5,
                            'maxlength' => 3000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Комментарий',
                            'placeholder' => 'Добавьте комментарий к спецификации',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Түсініктеме',
                            'placeholder' => 'Спецификацияға түсініктеме қосыңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Comment',
                            'placeholder' => 'Add a comment about the specification',
                            'description' => null,
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Обратный звонок
            |--------------------------------------------------------------------------
            */

            'callback' => [
                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваше имя',
                            'placeholder' => 'Введите ваше имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Атыңыз',
                            'placeholder' => 'Атыңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your name',
                            'placeholder' => 'Enter your name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'preferred_time',
                        'type' => 'text',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Удобное время для звонка',
                            'placeholder' => 'Например: с 14:00 до 17:00',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Қоңырау шалуға ыңғайлы уақыт',
                            'placeholder' => 'Мысалы: 14:00-ден 17:00-ге дейін',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Preferred call time',
                            'placeholder' => 'For example: 14:00 to 17:00',
                            'description' => null,
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Поставщик
            |--------------------------------------------------------------------------
            */

            'supplier' => [
                [
                    'data' => [
                        'name' => 'company',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Название компании',
                            'placeholder' => 'Введите название компании',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Компания атауы',
                            'placeholder' => 'Компания атауын енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Company name',
                            'placeholder' => 'Enter company name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Контактное лицо',
                            'placeholder' => 'Введите имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Байланыс тұлғасы',
                            'placeholder' => 'Атын енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Contact person',
                            'placeholder' => 'Enter name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'email',
                        'type' => 'email',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 400,
                        'default_value' => null,
                        'validation' => [
                            'email',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Email',
                            'placeholder' => 'company@example.kz',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Email',
                            'placeholder' => 'company@example.kz',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Email',
                            'placeholder' => 'company@example.kz',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'website',
                        'type' => 'url',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 500,
                        'default_value' => null,
                        'validation' => [
                            'url',
                            'max:1000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 1000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Сайт компании',
                            'placeholder' => 'https://example.kz',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Компания сайты',
                            'placeholder' => 'https://example.kz',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Company website',
                            'placeholder' => 'https://example.kz',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'product_categories',
                        'type' => 'checkbox_group',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 600,
                        'default_value' => null,
                        'validation' => [
                            'array',
                        ],
                        'width' => 'full',
                        'settings' => null,
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Категории продукции',
                            'placeholder' => null,
                            'description' => 'Выберите основные категории оборудования или продукции, которые поставляет ваша компания.',
                        ],
                        'kk' => [
                            'label' => 'Өнім санаттары',
                            'placeholder' => null,
                            'description' => 'Компанияңыз жеткізетін жабдықтың немесе өнімнің негізгі санаттарын таңдаңыз.',
                        ],
                        'en' => [
                            'label' => 'Product categories',
                            'placeholder' => null,
                            'description' => 'Select the main equipment or product categories supplied by your company.',
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'message',
                        'type' => 'textarea',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 700,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:3000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'rows' => 5,
                            'maxlength' => 3000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Комментарий',
                            'placeholder' => 'Расскажите о вашей компании и предлагаемой продукции',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Түсініктеме',
                            'placeholder' => 'Компанияңыз және ұсынатын өнімдеріңіз туралы жазыңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Comment',
                            'placeholder' => 'Tell us about your company and the products you supply',
                            'description' => null,
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Школа
            |--------------------------------------------------------------------------
            */

            'school' => [
                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваше имя',
                            'placeholder' => 'Введите ваше имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Атыңыз',
                            'placeholder' => 'Атыңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your name',
                            'placeholder' => 'Enter your name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'email',
                        'type' => 'email',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => [
                            'email',
                            'max:255',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'course_interest',
                        'type' => 'select',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 400,
                        'default_value' => null,
                        'validation' => null,
                        'width' => 'full',
                        'settings' => null,
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Направление обучения',
                            'placeholder' => 'Выберите направление',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Оқу бағыты',
                            'placeholder' => 'Бағытты таңдаңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Training area',
                            'placeholder' => 'Select a training area',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'message',
                        'type' => 'textarea',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 500,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:2000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'rows' => 5,
                            'maxlength' => 2000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Комментарий',
                            'placeholder' => 'Напишите, какое обучение вас интересует',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Түсініктеме',
                            'placeholder' => 'Қандай оқу сізді қызықтыратынын жазыңыз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Comment',
                            'placeholder' => 'Tell us what training you are interested in',
                            'description' => null,
                        ],
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Контактная форма
            |--------------------------------------------------------------------------
            */

            'contact' => [
                [
                    'data' => [
                        'name' => 'name',
                        'type' => 'text',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 100,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Ваше имя',
                            'placeholder' => 'Введите ваше имя',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Атыңыз',
                            'placeholder' => 'Атыңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Your name',
                            'placeholder' => 'Enter your name',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'phone',
                        'type' => 'tel',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 200,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:50',
                        ],
                        'width' => '1/2',
                        'settings' => [
                            'maxlength' => 50,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Телефон',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Phone',
                            'placeholder' => '+7 (___) ___-__-__',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'email',
                        'type' => 'email',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 300,
                        'default_value' => null,
                        'validation' => [
                            'email',
                            'max:255',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Email',
                            'placeholder' => 'example@mail.kz',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'subject',
                        'type' => 'text',
                        'activity' => true,
                        'required' => false,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 400,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:255',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'maxlength' => 255,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Тема обращения',
                            'placeholder' => 'Укажите тему обращения',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Өтініш тақырыбы',
                            'placeholder' => 'Өтініш тақырыбын көрсетіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Subject',
                            'placeholder' => 'Enter the subject of your enquiry',
                            'description' => null,
                        ],
                    ],
                ],

                [
                    'data' => [
                        'name' => 'message',
                        'type' => 'textarea',
                        'activity' => true,
                        'required' => true,
                        'readonly' => false,
                        'disabled' => false,
                        'sort' => 500,
                        'default_value' => null,
                        'validation' => [
                            'string',
                            'max:3000',
                        ],
                        'width' => 'full',
                        'settings' => [
                            'rows' => 6,
                            'maxlength' => 3000,
                        ],
                    ],

                    'translations' => [
                        'ru' => [
                            'label' => 'Сообщение',
                            'placeholder' => 'Введите ваше сообщение',
                            'description' => null,
                        ],
                        'kk' => [
                            'label' => 'Хабарлама',
                            'placeholder' => 'Хабарламаңызды енгізіңіз',
                            'description' => null,
                        ],
                        'en' => [
                            'label' => 'Message',
                            'placeholder' => 'Enter your message',
                            'description' => null,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($forms as $formCode => $fields) {
            /*
            |--------------------------------------------------------------------------
            | Получаем форму
            |--------------------------------------------------------------------------
            */

            $form = Form::query()
                ->where('code', $formCode)
                ->first();

            if (!$form) {
                throw new RuntimeException(
                    "Форма с кодом [{$formCode}] не найдена. "
                    . 'Сначала выполните FormSeeder.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Поля формы
            |--------------------------------------------------------------------------
            */

            foreach ($fields as $item) {
                $field = FormField::query()->updateOrCreate(
                    [
                        'form_id' => $form->id,
                        'name' => $item['data']['name'],
                    ],
                    [
                        'form_id' => $form->id,
                        'name' => $item['data']['name'],
                        'type' => $item['data']['type'],
                        'activity' => $item['data']['activity'],
                        'required' => $item['data']['required'],
                        'readonly' => $item['data']['readonly'],
                        'disabled' => $item['data']['disabled'],
                        'sort' => $item['data']['sort'],
                        'default_value' => $item['data']['default_value'],
                        'validation' => $item['data']['validation'],
                        'width' => $item['data']['width'],
                        'settings' => $item['data']['settings'],
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Переводы поля
                |--------------------------------------------------------------------------
                */

                foreach (
                    $item['translations']
                    as $locale => $translation
                ) {
                    $field->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                        ],
                        [
                            'label' => $translation['label'],
                            'placeholder' => $translation['placeholder'],
                            'description' => $translation['description'],
                        ]
                    );
                }
            }
        }
    }
}
