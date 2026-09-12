<?php

namespace App\Http\Middleware\Public;

use App\Context\Location\LocationContext;
use App\Services\Public\Location\LocationResolverService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

readonly class ResolveLocation
{
    public function __construct(
        private LocationResolverService $resolver,
        private LocationContext $context
    ) {
    }

    /**
     * Определить Location текущего запроса,
     * сохранить её в LocationContext,
     * установить как параметр маршрутов по умолчанию
     * и при необходимости привести публичный URL
     * к каноническому виду с Location.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /**
         * Административные маршруты используют LocationContext
         * и URL::defaults для генерации публичных ссылок,
         * но сами не должны содержать Location в URL.
         *
         * Пример:
         * /ru/admin
         * /ru/admin/...
         */
        $isAdmin =
            $request->routeIs('admin.*') ||
            $request->segment(2) === 'admin';

        $result = $this->resolver->resolve(
            $request
        );

        if (!$result) {
            return $next($request);
        }

        $location = $result['location'];

        $this->context->set(
            $location,
            $result['source']
        );

        /**
         * Location необходима также в админке,
         * чтобы Ziggy мог генерировать публичные маршруты,
         * содержащие обязательный параметр {location}.
         */
        URL::defaults([
            'location' => $location->slug,
        ]);

        /**
         * Если Laravel воспринял первый сегмент старого
         * публичного URL как {location}, но такой Location
         * не существует, вставляем текущую Location
         * после locale.
         *
         * Административные маршруты сюда не допускаем.
         *
         * Пример:
         * /ru/about
         * ↓
         * /ru/astana/about
         *
         * Но:
         * /ru/admin
         * остаётся:
         * /ru/admin
         */
        if (
            !$isAdmin &&
            $result['invalid_route_location']
        ) {
            $segments = $request->segments();

            /**
             * Текущий первый сегмент после locale
             * является невалидной "location",
             * поэтому вставляем настоящую Location перед ним.
             */
            array_splice(
                $segments,
                1,
                0,
                [$location->slug]
            );

            $url =
                url('/' . implode('/', $segments));

            if ($request->getQueryString()) {
                $url .= '?' . $request->getQueryString();
            }

            return redirect()->to($url);
        }

        return $next($request);
    }
}
