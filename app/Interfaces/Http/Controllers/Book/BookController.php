<?php

namespace App\Interfaces\Http\Controllers\Book;

use App\Application\Book\DTO\CreateBookInput;
use App\Application\Book\DTO\PatchBookDTO;
use App\Application\Book\UseCases\CreateBook;
use App\Application\Book\UseCases\DeleteBook;
use App\Application\Book\UseCases\GetAllBooks;
use App\Application\Book\UseCases\GetBookById;
use App\Application\Book\UseCases\PatchBook;
use App\Interfaces\Http\Controllers\Controller;
use App\Interfaces\Http\Requests\Book\CreateBookRequest;
use App\Interfaces\Http\Requests\Book\PatchBookRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\PathItem(
 *     path="/api/v1/books"
 * )
 */
class BookController extends Controller
{
    public function __construct(
        private CreateBook $createBook,
        private GetAllBooks $getAllBooks,
        private PatchBook $patchBook,
        private DeleteBook $deleteBook,
        private GetBookById  $getBookById,
    ){

    }

    /**
     * @OA\Get(
     *     path="/v1/books",
     *     tags={"Books"},
     *     summary="List books",
     *
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="perPage", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sortBy", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="orderBy", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="genre", in="query", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function index(Request $request){
        $bookDTO = $this->getAllBooks->getAll($request);
        return response()->json([
            'data' => $bookDTO->toArray()
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/v1/books/{id}",
     *     tags={"Books"},
     *     summary="fetch a book by id",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function show(int $id){
        $book = $this->getBookById->execute($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'data' => $book,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/v1/books",
     *     tags={"Books"},
     *     summary="Create a book",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","author","isbn","stock"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="isbn", type="string"),
     *             @OA\Property(property="genres", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="stock", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/v1/books/{id}",
     *     tags={"Books"},
     *     summary="Delete a book",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id){
        try {
            $book = $this->deleteBook->execute($id);
            if (!$book) {
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

    /**
     * @OA\Patch(
     *     path="/v1/books/{id}",
     *     tags={"Books"},
     *     summary="Update a book",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="isbn", type="string"),
     *             @OA\Property(property="genres", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="stock", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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
