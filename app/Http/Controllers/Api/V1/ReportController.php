<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\StoreFormRequest;
use App\Http\Requests\Admin\Report\UpdateFormRequest;
use App\Http\Resources\V1\ReportCollection;
use App\Http\Resources\V1\ReportResource;
use App\Models\Report;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *     title="API Doc",
 *     version="1.0.0"
 * ),
 * @OA\PathItem(
 *     path="/api/"
 * )
 *
 * @OA\Post(
 *     path="/api/v1/reports",
 *     summary="Создание отчета",
 *     tags={"Report"},
 *
 *     @OA\RequestBody(
 *          @OA\JsonContent(
 *              allOf={
 *                  @OA\Schema(
 *                      @OA\Property(property="status", type="string", example="Created"),
 *                      @OA\Property(property="utility_id", type="integer", example=12351),
 *                      @OA\Property(property="project_id", type="integer", example=678067),
 *                  )
 *              }
 *          )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                  @OA\Property(property="id", type="integer", example=647),
 *                  @OA\Property(property="utility_id", type="integer", example=151),
 *                  @OA\Property(property="project_id", type="integer", example=6067),
 *              ),
 *         )
 *     ),
 * ),
 * @OA\Get (
 *      path="/api/v1/reports",
 *      summary="Получение списка отчетов",
 *      tags={"Report"},
 *
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              @OA\Property(property="data", type="array", @OA\Items(
 *                  @OA\Property(property="id", type="integer", example=647),
 *                    @OA\Property(property="utility_id", type="integer", example=151),
 *                    @OA\Property(property="project_id", type="integer", example=6067),
 *              )),
 *          )
 *      ),
 *  ),
 * @OA\Get (
 *       path="/api/v1/reports/{report}",
 *       summary="Получение отчета",
 *       tags={"Report"},
 *       @OA\Parameter(
 *           description="ID отчета",
 *           name="report",
 *           in="path",
 *           required=true,
 *           example=647
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="OK",
 *           @OA\JsonContent(
 *               @OA\Property(property="data", type="object",
 *                   @OA\Property(property="id", type="integer", example=647),
 *                      @OA\Property(property="status", type="string", example="Created"),
 *                     @OA\Property(property="utility_id", type="integer", example=151),
 *                     @OA\Property(property="project_id", type="integer", example=6067),
 *               ),
 *           )
 *       ),
 *   ),
 * @OA\Put (
 *        path="/api/v1/reports/{report}",
 *        summary="Обновление отчета",
 *        tags={"Report"},
 *     @OA\RequestBody(
 *           @OA\JsonContent(
 *               allOf={
 *                   @OA\Schema(
 *                       @OA\Property(property="status", type="string", example="Created"),
 *                       @OA\Property(property="utility_id", type="integer", example=12351),
 *                       @OA\Property(property="project_id", type="integer", example=678067),
 *                   )
 *               }
 *           )
 *      ),
 *        @OA\Parameter(
 *            description="ID отчета",
 *            name="report",
 *            in="path",
 *            required=true,
 *            example=647
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="OK",
 *            @OA\JsonContent(
 *                @OA\Property(property="data", type="object",
 *                    @OA\Property(property="id", type="integer", example=647),
 *                       @OA\Property(property="status", type="string", example="Created"),
 *                      @OA\Property(property="utility_id", type="integer", example=151),
 *                      @OA\Property(property="project_id", type="integer", example=6067),
 *                ),
 *            )
 *        ),
 *    ),
 * @OA\Delete (
 *        path="/api/v1/reports/{report}",
 *        summary="Удаление отчета",
 *        tags={"Report"},
 *        @OA\Parameter(
 *            description="ID отчета",
 *            name="report",
 *            in="path",
 *            required=true,
 *            example=647
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="OK",
 *        ),
 *    ),
 */
class ReportController extends Controller
{

    public function index()
    {
        return new ReportCollection(Report::all());
    }


    public function store(StoreFormRequest $request)
    {
        $data = $request->validated();
        $report = Report::create($data);

        return ReportResource::make($report);
    }


    public function show(string $id)
    {
        return new ReportResource(Report::find($id));
    }


    public function update(UpdateFormRequest $request, string $id)
    {
        $data = $request->validated();
        $report = Report::find($id);
        $report->update($data);

        return ReportResource::make($report);
    }


    public function destroy(string $id)
    {
        Report::findOrFail($id)->delete();

        return response()->json([
            'message' => 'OK'
        ]);
    }
}
