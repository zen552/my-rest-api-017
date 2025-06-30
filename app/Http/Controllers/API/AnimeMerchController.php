<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\AnimeMerch;
use OpenApi\Annotations as OA;

/**
 * Class AnimeMerchController
 * 
 * @author Alvin <alvin.422024017@civitas.ukrida.ac.id>
 */

class AnimeMerchController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/anime_merch",
     *     tags={"Anime Merch"},
     *     summary="Display a listing of anime merchandise",
     *     operationId="indexAnimeMerch",
     *     @OA\Response(
     *         response=200,
     *         description="successful",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index()
    {
        $merch = AnimeMerch::all();
        return response()->json(array('message'=>'Data retrieved successfully', 'data'=>$merch), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/anime_merch",
     *     tags={"Anime Merch"},
     *     summary="Store a newly created anime merchandise",
     *     operationId="storeAnimeMerch",
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body description",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/AnimeMerch",
     *             example={"nama_item": "One Piece Mug", "Producer": "Toei Animation", "tahun_rilis": "2023", "gambar": "https://example.com/cover.jpg", "description": "Mug edisi khusus One Piece Gear 5", "harga": 75000}
     *         ),
     *     ),
     *     security={{"pasport_token_ready":{}, "passport":{}}}
     * )
    */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'nama_item'  => 'required|unique:anime_merch',
                'producer'   => 'nullable|max:100',
                'harga'      => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
            $merch = new AnimeMerch();
            $merch->fill($request->all())->save();
            return response()->json(array('message'=>'Saved successfully', 'data'=>$merch), 200);
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Get(
     *     path="/api/anime_merch/{id}",
     *     tags={"Anime Merch"},
     *     summary="Display the specified anime merchandise item",
     *     operationId="showAnimeMerch",
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of anime merchandise",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $merch = AnimeMerch::findOrFail($id);
        return response()->json(array('message'=>'Data detail retrieved successfully', 'data'=>$merch), 200);
    }
    
    /**
     * @OA\Put(
     *     path="/api/anime_merch/{id}",
     *     tags={"Anime Merch"},
     *     summary="Update an anime merchandise item",
     *     operationId="updateAnimeMerch",
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of anime merchandise that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body description",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/AnimeMerch",
     *             example={"nama_item": "Naruto Hoodie", "producer": "Studio Pierrot", "tahun_rilis": "2023", "gambar": "https://example.com/cover.jpg", "description": "Hoodie keren bergambar Naruto", "harga": 120000}
     *         )
     *     ),
     *     security={{"passport_token_ready":{}, "passport":{}}}
     * )
    */
    public function update(Request $request, $id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $merch = AnimeMerch::find($id);
        if (!$merch) {
            throw new HttpException(404, 'Item not found');
        }

        try {
            $merch->fill($request->all())->save();
            return response()->json(array('message'=>'Updated successfully', 'data'=>$merch), 200);
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/anime_merch/{id}",
     *     tags={"Anime Merch"},
     *     summary="Remove an anime merchandise item",
     *     operationId="destroyAnimeMerch",
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of anime merchandise that needs to be removed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     security={{"passport_token_ready"={}, "passport"={}}}
     * )
     */
    public function destroy($id)
    {
        $merch = AnimeMerch::findOrFail($id);
        $merch->delete();

        return response()->json(array('message'=>'Deleted successfully', 'data'=>$merch), 204);
    }
}