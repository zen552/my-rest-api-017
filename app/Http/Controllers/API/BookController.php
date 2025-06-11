<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $book = Book::all();
        return response()->json(array('message'=>'Data retrieved successfully', 'data'=>$book), 200);
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title'  => 'required|unique:books',
                'author' => 'required|max:100',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
            $book = new Book;
            $book->fill($request->all())->save();
            return response()->json(array('message'=>'Saved successfully', 'data'=>$book), 200);
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        return response()->json(array('message'=>'Data detail retrieved successfully', 'data'=>$book), 200);
    }
    
    public function update(Request $request, $id)
    {
        if (!$id) {
            throw new HttpException(400, "Invalid id");
        }
        $book = Book::find($id);
        if (!$book) {
            throw new HttpException(404, 'Item not found');
        }

        try {
            $book->fill($request->all())->save();
            return response()->json(array('message'=>'Updated successfully', 'data'=>$book), 200);
        } catch (\Exception $exception) {
            throw new HttpException(400, "Invalid data - {$exception->getMessage()}");
        }
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(array('message'=>'Deleted successfully', 'data'=>$book), 204);
    }
}