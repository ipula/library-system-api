<?php

namespace App\Interfaces\Http\Controllers\BookRental;

use App\Application\Book\UseCases\PatchBook;
use App\Application\BookRental\UseCases\ExtendRental;
use App\Application\BookRental\UseCases\FinishRental;
use App\Application\BookRental\UseCases\RentABook;
use App\Application\BookRental\UseCases\RentalReadingProgress;
use App\Application\BookRental\UseCases\UpdateProgress;
use App\Interfaces\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Book rental",
 *     description="Book rental management"
 * )
 */

class BookRentalController extends Controller
{
    public function __construct(
        private RentABook $rentABook,
        private ExtendRental $extendRental,
        private UpdateProgress $updateProgress,
        private FinishRental $finishRental,
        private RentalReadingProgress $getReadingProgress,
    ){}

    /**
     * @OA\Get(
     *     path="/v1/getRentalReadingProgress/{rentalId}",
     *     tags={"Rentals"},
     *     summary="Get reading progress for a rental",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="rentalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Rental not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function rentalReadingProgress(int $rentalId): \Illuminate\Http\JsonResponse
    {
        $progress = $this->getReadingProgress->execute($rentalId);

        return response()->json([
            'data' => [
                'rental_id' => $rentalId,
                'progress'  => $progress,
            ],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/v1/rentBooks",
     *     tags={"Rentals"},
     *     summary="Rent a book",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"book_id"},
     *             @OA\Property(property="book_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Book rented"),
     *     @OA\Response(response=422, description="Book not available or validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function rentBooks(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'book_id' => 'required|integer',
        ]);
        $userId = $request->user()->id;
        $rentalDTO = $this->rentABook->execute($userId,$data['book_id']);
        return response()->json([
            'data'    => $rentalDTO->toArray(),
            'message' => 'Book rented successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Patch(
     *     path="/v1/rentExtend/{rentalId}",
     *     tags={"Rentals"},
     *     summary="Extend a rental",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="rentalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"extendedDate"},
     *             @OA\Property(property="extendedDate", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Extended"),
     *     @OA\Response(response=404, description="Rental not found"),
     *     @OA\Response(response=409, description="Rental already finished"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function extendRental(Request $request,int $rentalId): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'extendedDate' => 'required|date',
        ]);
        $extendedDate = new \DateTimeImmutable($data['extendedDate']);
        $rentalDTO = $this->extendRental->execute($rentalId,$extendedDate);
        return response()->json([
            'data'    => $rentalDTO->toArray(),
            'message' => 'Book rent extended successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Patch(
     *     path="/v1/updateRentProgress/{rentalId}",
     *     tags={"Rentals"},
     *     summary="Update reading progress",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="rentalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"progress"},
     *             @OA\Property(property="progress", type="number", format="float", example=50)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Progress updated"),
     *     @OA\Response(response=404, description="Rental not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function updateProgress(Request $request,int $rentalId): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'progress' => 'required|decimal:2',
        ]);
        $rentalDTO = $this->updateProgress->execute($rentalId,$data['progress']);
        return response()->json([
            'data'    => $rentalDTO->toArray(),
            'message' => 'Book rent extended successfully',
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Patch(
     *     path="/v1/rentFinish/{rentalId}",
     *     tags={"Rentals"},
     *     summary="Finish a rental",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="rentalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Rental finished"),
     *     @OA\Response(response=404, description="Rental not found"),
     *     @OA\Response(response=409, description="Rental already finished"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function finishRental(PatchBook $request,int $rentalId): \Illuminate\Http\JsonResponse
    {
        $rentalDTO = $this->finishRental->execute($rentalId);
        return response()->json([
            'data'    => $rentalDTO->toArray(),
            'message' => 'Book rent extended successfully',
        ], Response::HTTP_CREATED);
    }
}
