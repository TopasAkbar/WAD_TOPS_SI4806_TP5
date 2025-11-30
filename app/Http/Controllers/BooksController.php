<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BooksController extends Controller
{
    /**
     * ==========1===========
     * Tampilkan daftar semua buku
     */
    public function index()
    {
        $books = Book::all();
        return Bookresource::collection($books);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' =>  'required|string|max:255',
            'author' =>  'required|string|max:255',
            'published_year' =>  'required|integer'

        ]);

        if ($validator->fails())
        {
            return response()->json(
                [
                    'message' => 'validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

        $book = Book::create($validator->validate());
        return (new BookResource($book))
        -> additional(['message' => 'Item created successfully'])
        -> response()
        -> setStatusCode(201);

    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);
        if (!$book)
        {
            return response()->json(['message'=>'book not found'], 404);
        }
        return new BookResource($book);

    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' =>  'required|string|max:255',
            'author' =>  'required|string|max:255',
            'published_year' =>  'required|integer'

        ]);
        $book = Book::find($id);

        if (!$book){
            return response()->json(['message'=>'book not found'], 404);
        }

        if ($validator->fails()){
            return response()->json(
                [
                  'message' => 'validation failed',
                    'errors' => $validator->errors()   
                ], 422);
        }
        $book->update($validator->validated());

        return (new BookResource($book))
        -> additional(['message' => 'book created successfully'])
        -> response()
        -> setStatusCode(201);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if(!$book)
        {
            return response()->json(['message'=>'book not found'], 404);
        }
        $book -> delete();
         return response()->json(['message'=>'book deleted succesfully'], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);

        if (!$book){
            return response([
                'message' => 'book not found'
            ], 404);
        }
        $book->is_available = !$book->is_available;
        $book->save();

        return new BookResource($book);

    }
}
