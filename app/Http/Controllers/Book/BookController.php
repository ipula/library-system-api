<?php

namespace App\Http\Controllers\Book;

use App\Application\Book\DTO\CreateBookInput;
use App\Application\Book\UseCases\CreateBook;
use App\Application\Book\UseCases\GetAllBooks;
use App\Domain\Book\Repositories\BookRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\CreateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{
    public function __construct(
        private CreateBook $createBook,
        private GetAllBooks $getAllBooks,
    ){

    }

    public function index(Request $request){
        $bookDTO = $this->getAllBooks->getAll($request);
        return response()->json([
            'data' => $bookDTO->toArray()
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request){}
    public function store(CreateBookRequest $request){
        try {
            $input = new CreateBookInput(
                title: $request->validated('title'),
                author: $request->validated('author'),
                genre: $request->validated('genres'),
                isbn: $request->validated('isbn'),
                description: $request->validated('description'),
                stock: $request->validated('stock'),
            );

            $bookDTO = $this->createBook->execute($input);

            return response()->json([
                'data' => $bookDTO->toArray(),
                'message' => 'book created successfully'
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function update(Request $request){}
    public function destroy(Request $request){}
}
