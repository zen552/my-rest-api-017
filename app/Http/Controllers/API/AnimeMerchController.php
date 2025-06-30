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
     *     ),
     *     @OA\Parameter(
     *         name="_page",
     *         in="query",
     *         description="current page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_limit",
     *         in="query",
     *         description="max item in a page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_search",
     *         in="query",
     *         description="word to search",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_Producer",
     *         in="query",
     *         description="search by Producer like name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_sort_by",
     *         in="query",
     *         description="word to search",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="latest"
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        try {
            $data['filter']         = $request->all();
            $page                   = @$data['filter']['_page'] ? intval($data['filter']['_page']) : 1;
            $limit                  = @$data['filter']['_limit'] ? intval($data['filter']['_limit']) : 1000;
            $offset                 = ($page-1)*$limit;
            $data['products']       = AnimeMerch::whereRaw('1 = 1');

            if($request->get('_search')){
                $data['products'] = $data['products']->whereRaw('(LOWER(nama_item) LIKE "%'.strtolower($request->get('_search')).'%" OR LOWER(Producer) LIKE "%'.strtolower($request->get('_search')).'%")');
            }
            if($request->get('_Producer')){
                $data['products'] = $data['products']->whereRaw('LOWER(Producer) = "'.strtolower($request->get('_Producer')).'"');
            }
            if($request->get('_sort_by')){
            switch ($request->get('_sort_by')) {
                default:
                case 'latest_tahun_rilis':
                    $data['products'] = $data['products']->orderBy('tahun_rilis','DESC');
                    break;
                case 'latest_added':
                    $data['products'] = $data['products']->orderBy('created_at','DESC');
                    break;
                case 'nama_item_asc':
                    $data['products'] = $data['products']->orderBy('nama_item','ASC');
                    break;
                case 'nama_item_desc':
                    $data['products'] = $data['products']->orderBy('nama_item','DESC');
                    break;
                case 'harga_asc':
                    $data['products'] = $data['products']->orderBy('harga','ASC');
                    break;
                case 'harga_desc':
                    $data['products'] = $data['products']->orderBy('harga','DESC');
                    break;
            }
            }
            $data['products_count_total'] = $data['products']->count();
            $data['products']             = ($limit==0 && $offset==0)?$data['products']:$data['products']->limit($limit)->offset($offset);
            // $data['products_raw_sql']  = $data['products']->toSql();
            $data['products']             = $data['products']->get();
            $data['products_count_start']= ($data['products_count_total'] == 0 ? 0 : (($page-1)*$limit)+1);
            $data['products_count_end']  = ($data['products_count_total'] == 0 ? 0 : (($page-1)*$limit)+sizeof($data['products']));
            return response()->json($data, 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data : {$exception->getMessage()}");
        }
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
     *     security={{"passport_token_ready":{}, "passport":{}}}
     * )
    */
    public function store(Request $request)
    {
        try {
            $data = $request->json()->all();
            $validator = Validator::make($data, [
                'nama_item'  => 'required|unique:anime_merch',
                'Producer'   => 'nullable|max:100',
                'harga'      => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
            $merch = new AnimeMerch();
            $merch->fill($request->all())->save();
            $merch->created_by = \Auth::user()->id; //store user ID
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
     *         ),
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
            $merch->updated_by = \Auth::user()->id; //set user ID on update
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
        $merch->deleted_by = \Auth::user()->id; // Track who deleted the record
        $merch->delete();

        return response()->json(array('message'=>'Deleted successfully', 'data'=>$merch), 204);
    }
}