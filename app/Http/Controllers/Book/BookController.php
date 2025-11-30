<?php

namespace App\Http\Controllers\Book;

use App\Application\Book\DTO\CreateBookInput;
use App\Application\Book\DTO\PatchBookDTO;
use App\Application\Book\UseCases\CreateBook;
use App\Application\Book\UseCases\DeleteBook;
use App\Application\Book\UseCases\GetAllBooks;
use App\Application\Book\UseCases\PatchBook;
use App\Domain\Book\Repositories\BookRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\CreateBookRequest;
use App\Http\Requests\Book\PatchBookRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{
    public function __construct(
        private CreateBook $createBook,
        private GetAllBooks $getAllBooks,
        private PatchBook $patchBook,
        private DeleteBook $deleteBook,
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
    public function destroy(int $id){
        try {
            $bookDTO = $this->deleteBook->execute($id);
            if (!$bookDTO) {
                return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'book deleted successfully'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(PatchBookRequest $request, int $id){
        try {
            $dto = new PatchBookDTO(
                id: $id,
                data: $request->validated()
            );
            $bookDTO = $this->patchBook->execute($dto);
            if (!$bookDTO) {
                return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'data' => $bookDTO,
                'message' => 'book updated successfully'
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
