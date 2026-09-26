<?php

namespace Database\Seeders;

use App\Models\Admin\Form\Form\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Заполнение системных форм приложения.
     */
    public function run(): void
    {
        $forms = [
            /*
            |--------------------------------------------------------------------------
            | Консультация
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'consultation',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 100,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Получить консультацию',
                        'subtitle' => 'Поможем разобраться в вашем вопросе',
                        'description' => 'Оставьте заявку, и наш специалист свяжется с вами для консультации.',
                        'submit_text' => 'Отправить заявку',
                        'success_message' => 'Спасибо! Ваша заявка успешно отправлена. Мы свяжемся с вами в ближайшее время.',
                        'error_message' => 'Не удалось отправить заявку. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Кеңес алу',
                        'subtitle' => 'Сұрағыңызды шешуге көмектесеміз',
                        'description' => 'Өтінім қалдырыңыз, біздің маман сізбен кеңес беру үшін хабарласады.',
                        'submit_text' => 'Өтінімді жіберу',
                        'success_message' => 'Рақмет! Өтініміңіз сәтті жіберілді. Біз сізбен жақын арада хабарласамыз.',
                        'error_message' => 'Өтінімді жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Get a consultation',
                        'subtitle' => 'We will help you find the right solution',
                        'description' => 'Submit a request and our specialist will contact you for a consultation.',
                        'submit_text' => 'Submit request',
                        'success_message' => 'Thank you! Your request has been submitted successfully. We will contact you shortly.',
                        'error_message' => 'Unable to submit your request. Please try again.',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Подбор оборудования
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'selection',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 200,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Подбор оборудования',
                        'subtitle' => 'Подберём оборудование под параметры вашего объекта',
                        'description' => 'Укажите основные параметры и контактные данные. Наш специалист подготовит подходящие варианты оборудования.',
                        'submit_text' => 'Подобрать оборудование',
                        'success_message' => 'Спасибо! Заявка на подбор оборудования успешно отправлена.',
                        'error_message' => 'Не удалось отправить заявку на подбор оборудования. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Жабдықты таңдау',
                        'subtitle' => 'Нысаныңыздың параметрлеріне сәйкес жабдықты таңдаймыз',
                        'description' => 'Негізгі параметрлер мен байланыс деректерін көрсетіңіз. Біздің маман жабдықтың қолайлы нұсқаларын дайындайды.',
                        'submit_text' => 'Жабдықты таңдау',
                        'success_message' => 'Рақмет! Жабдықты таңдау туралы өтінім сәтті жіберілді.',
                        'error_message' => 'Өтінімді жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Equipment selection',
                        'subtitle' => 'We will select equipment for your project requirements',
                        'description' => 'Provide the main project parameters and your contact details. Our specialist will prepare suitable equipment options.',
                        'submit_text' => 'Select equipment',
                        'success_message' => 'Thank you! Your equipment selection request has been submitted successfully.',
                        'error_message' => 'Unable to submit your equipment selection request. Please try again.',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Заявка по спецификации
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'specification',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 300,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Отправить спецификацию',
                        'subtitle' => 'Прикрепите спецификацию или техническое задание',
                        'description' => 'Отправьте нам спецификацию, ведомость оборудования или техническое задание для расчёта и подготовки предложения.',
                        'submit_text' => 'Отправить спецификацию',
                        'success_message' => 'Спасибо! Спецификация успешно отправлена и принята в обработку.',
                        'error_message' => 'Не удалось отправить спецификацию. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Спецификацияны жіберу',
                        'subtitle' => 'Спецификацияны немесе техникалық тапсырманы тіркеңіз',
                        'description' => 'Есептеу және ұсыныс дайындау үшін бізге спецификацияны, жабдық тізімін немесе техникалық тапсырманы жіберіңіз.',
                        'submit_text' => 'Спецификацияны жіберу',
                        'success_message' => 'Рақмет! Спецификация сәтті жіберілді және өңдеуге қабылданды.',
                        'error_message' => 'Спецификацияны жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Submit a specification',
                        'subtitle' => 'Attach your specification or technical requirements',
                        'description' => 'Send us your specification, equipment schedule or technical requirements for calculation and proposal preparation.',
                        'submit_text' => 'Submit specification',
                        'success_message' => 'Thank you! Your specification has been submitted successfully and accepted for processing.',
                        'error_message' => 'Unable to submit the specification. Please try again.',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Обратный звонок
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'callback',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 400,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Заказать обратный звонок',
                        'subtitle' => 'Мы свяжемся с вами',
                        'description' => 'Оставьте номер телефона, и наш специалист перезвонит вам.',
                        'submit_text' => 'Заказать звонок',
                        'success_message' => 'Спасибо! Заявка на обратный звонок успешно отправлена.',
                        'error_message' => 'Не удалось заказать обратный звонок. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Кері қоңырауға тапсырыс беру',
                        'subtitle' => 'Біз сізбен хабарласамыз',
                        'description' => 'Телефон нөміріңізді қалдырыңыз, біздің маман сізге қоңырау шалады.',
                        'submit_text' => 'Қоңырауға тапсырыс беру',
                        'success_message' => 'Рақмет! Кері қоңырауға өтінім сәтті жіберілді.',
                        'error_message' => 'Кері қоңырауға өтінім жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Request a callback',
                        'subtitle' => 'We will contact you',
                        'description' => 'Leave your phone number and our specialist will call you back.',
                        'submit_text' => 'Request callback',
                        'success_message' => 'Thank you! Your callback request has been submitted successfully.',
                        'error_message' => 'Unable to submit your callback request. Please try again.',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Стать поставщиком
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'supplier',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 500,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Стать поставщиком',
                        'subtitle' => 'Размещайте товары и оборудование на нашей площадке',
                        'description' => 'Оставьте информацию о вашей компании. Мы свяжемся с вами и расскажем об условиях сотрудничества.',
                        'submit_text' => 'Отправить заявку',
                        'success_message' => 'Спасибо! Заявка поставщика успешно отправлена.',
                        'error_message' => 'Не удалось отправить заявку поставщика. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Жеткізуші болу',
                        'subtitle' => 'Тауарлар мен жабдықтарды біздің алаңда орналастырыңыз',
                        'description' => 'Компанияңыз туралы ақпарат қалдырыңыз. Біз сізбен хабарласып, ынтымақтастық шарттары туралы айтып береміз.',
                        'submit_text' => 'Өтінімді жіберу',
                        'success_message' => 'Рақмет! Жеткізушінің өтінімі сәтті жіберілді.',
                        'error_message' => 'Жеткізушінің өтінімін жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Become a supplier',
                        'subtitle' => 'List your products and equipment on our marketplace',
                        'description' => 'Tell us about your company. We will contact you and explain the terms of cooperation.',
                        'submit_text' => 'Submit application',
                        'success_message' => 'Thank you! Your supplier application has been submitted successfully.',
                        'error_message' => 'Unable to submit your supplier application. Please try again.',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Обучение
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'school',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 600,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Обучение',
                        'subtitle' => 'Получите информацию об обучении',
                        'description' => 'Оставьте заявку, если вам нужна дополнительная информация о курсах и программах обучения.',
                        'submit_text' => 'Отправить заявку',
                        'success_message' => 'Спасибо! Ваша заявка на обучение успешно отправлена.',
                        'error_message' => 'Не удалось отправить заявку. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Оқу',
                        'subtitle' => 'Оқу туралы ақпарат алыңыз',
                        'description' => 'Курстар мен оқу бағдарламалары туралы қосымша ақпарат қажет болса, өтінім қалдырыңыз.',
                        'submit_text' => 'Өтінімді жіберу',
                        'success_message' => 'Рақмет! Оқу туралы өтініміңіз сәтті жіберілді.',
                        'error_message' => 'Өтінімді жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Training',
                        'subtitle' => 'Learn more about our training programs',
                        'description' => 'Submit a request if you would like more information about courses and training programs.',
                        'submit_text' => 'Submit request',
                        'success_message' => 'Thank you! Your training request has been submitted successfully.',
                        'error_message' => 'Unable to submit your request. Please try again.',
                    ],
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Контактная форма
            |--------------------------------------------------------------------------
            */

            [
                'data' => [
                    'user_id' => null,
                    'code' => 'contact',
                    'status' => Form::STATUS_PUBLISHED,
                    'activity' => true,
                    'sort' => 700,

                    'spam_protection' => true,
                    'honeypot_enabled' => true,
                    'min_submit_seconds' => 3,
                    'rate_limit' => 5,
                    'rate_limit_minutes' => 10,
                    'captcha_enabled' => false,

                    'auth_required' => false,

                    'settings' => null,
                ],

                'translations' => [
                    'ru' => [
                        'title' => 'Связаться с нами',
                        'subtitle' => 'Напишите нам',
                        'description' => 'Заполните форму, и мы свяжемся с вами для ответа на ваш вопрос.',
                        'submit_text' => 'Отправить сообщение',
                        'success_message' => 'Спасибо! Ваше сообщение успешно отправлено.',
                        'error_message' => 'Не удалось отправить сообщение. Пожалуйста, попробуйте ещё раз.',
                    ],

                    'kk' => [
                        'title' => 'Бізбен байланысу',
                        'subtitle' => 'Бізге жазыңыз',
                        'description' => 'Форманы толтырыңыз, біз сіздің сұрағыңызға жауап беру үшін хабарласамыз.',
                        'submit_text' => 'Хабарламаны жіберу',
                        'success_message' => 'Рақмет! Хабарламаңыз сәтті жіберілді.',
                        'error_message' => 'Хабарламаны жіберу мүмкін болмады. Қайталап көріңіз.',
                    ],

                    'en' => [
                        'title' => 'Contact us',
                        'subtitle' => 'Send us a message',
                        'description' => 'Complete the form and we will contact you regarding your enquiry.',
                        'submit_text' => 'Send message',
                        'success_message' => 'Thank you! Your message has been sent successfully.',
                        'error_message' => 'Unable to send your message. Please try again.',
                    ],
                ],
            ],
        ];

        foreach ($forms as $item) {
            /*
            |--------------------------------------------------------------------------
            | Форма
            |--------------------------------------------------------------------------
            */

            $form = Form::query()->updateOrCreate(
                [
                    'code' => $item['data']['code'],
                ],
                $item['data']
            );

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            foreach ($item['translations'] as $locale => $translation) {
                $form->translations()->updateOrCreate(
                    [
                        'locale' => $locale,
                    ],
                    [
                        'title' => $translation['title'],
                        'subtitle' => $translation['subtitle'],
                        'description' => $translation['description'],
                        'submit_text' => $translation['submit_text'],
                        'success_message' => $translation['success_message'],
                        'error_message' => $translation['error_message'],
                    ]
                );
            }
        }
    }
}
