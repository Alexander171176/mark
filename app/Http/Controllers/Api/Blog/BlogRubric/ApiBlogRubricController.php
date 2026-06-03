<?php

namespace App\Http\Controllers\Api\Blog\BlogRubric;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogRubric\BlogRubricRequest;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricResource;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Blog Rubrics",
 *     description="API управления рубриками блога"
 * )
 *
 * @OA\Schema(
 *     schema="BlogRubric",
 *     type="object",
 *     title="BlogRubric",
 *     description="Модель рубрики блога",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="sort", type="integer", example=0),
 *     @OA\Property(property="activity", type="boolean", example=true),
 *     @OA\Property(property="url", type="string", example="novosti"),
 *     @OA\Property(property="level", type="integer", example=1),
 *     @OA\Property(property="views", type="integer", example=10),
 *     @OA\Property(property="translation", type="object"),
 *     @OA\Property(property="translations", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="images", type="array", @OA\Items(type="object"))
 * )
 */
class ApiBlogRubricController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/blog-rubrics",
     *     operationId="getBlogRubrics",
     *     tags={"Blog Rubrics"},
     *     summary="Получить список рубрик блога",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="rubrics",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/BlogRubric")
     *             ),
     *
     *             @OA\Property(
     *                 property="rubricsCount",
     *                 type="integer",
     *                 example=12
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $rubrics = BlogRubric::query()
            ->with([
                'translations',
                'images',
            ])
            ->withCount([
                'articles',
                'images',
            ])
            ->ordered()
            ->get();

        return response()->json([
            'rubrics' => BlogRubricResource::collection($rubrics),
            'rubricsCount' => BlogRubric::query()->count(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/blog-rubrics",
     *     operationId="storeBlogRubric",
     *     tags={"Blog Rubrics"},
     *     summary="Создать рубрику блога",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogRubric")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Рубрика успешно создана",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogRubric")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function store(BlogRubricRequest $request): JsonResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        $rubric = DB::transaction(function () use (
            $data,
            $translations
        ) {
            if (!isset($data['sort']) || is_null($data['sort'])) {
                $maxSort = BlogRubric::query()
                    ->where('parent_id', $data['parent_id'] ?? null)
                    ->max('sort');

                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            $rubric = BlogRubric::create($data);

            foreach ($translations as $locale => $translation) {
                $rubric->translations()->create([
                    ...$translation,
                    'locale' => $locale,
                ]);
            }

            return $rubric;
        });

        $rubric->load([
            'translations',
            'images',
        ]);

        Log::info('API - Рубрика блога создана', [
            'id' => $rubric->id,
        ]);

        return response()->json(
            new BlogRubricResource($rubric),
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/blog-rubrics/{id}",
     *     operationId="showBlogRubric",
     *     tags={"Blog Rubrics"},
     *     summary="Получить рубрику блога",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogRubric")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Рубрика не найдена"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $rubric = BlogRubric::query()
            ->with([
                'translations',
                'images',
            ])
            ->withCount([
                'articles',
                'images',
            ])
            ->findOrFail($id);

        return response()->json(
            new BlogRubricResource($rubric)
        );
    }

    /**
     * @OA\Put(
     *     path="/api/blog-rubrics/{id}",
     *     operationId="updateBlogRubric",
     *     tags={"Blog Rubrics"},
     *     summary="Обновить рубрику блога",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogRubric")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Рубрика успешно обновлена",
     *
     *         @OA\JsonContent(ref="#/components/schemas/BlogRubric")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Рубрика не найдена"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function update(
        BlogRubricRequest $request,
        string $id
    ): JsonResponse {
        $rubric = BlogRubric::query()
            ->findOrFail($id);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        DB::transaction(function () use (
            $rubric,
            $data,
            $translations
        ) {
            $rubric->update($data);

            foreach ($translations as $locale => $translation) {
                $rubric->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $translation
                );
            }
        });

        $rubric->load([
            'translations',
            'images',
        ]);

        Log::info('API - Рубрика блога обновлена', [
            'id' => $rubric->id,
        ]);

        return response()->json(
            new BlogRubricResource($rubric)
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/blog-rubrics/{id}",
     *     operationId="deleteBlogRubric",
     *     tags={"Blog Rubrics"},
     *     summary="Удалить рубрику блога",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Рубрика успешно удалена"
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Рубрика не найдена"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $rubric = BlogRubric::query()
            ->findOrFail($id);

        DB::transaction(function () use ($rubric) {
            $rubric->translations()->delete();
            $rubric->images()->detach();
            $rubric->delete();
        });

        Log::info('API - Рубрика блога удалена', [
            'id' => $id,
        ]);

        return response()->json(
            null,
            204
        );
    }
}
