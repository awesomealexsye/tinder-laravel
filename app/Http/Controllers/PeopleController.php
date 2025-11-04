<?php

namespace App\Http\Controllers;

use App\Factories\ResponseFactory;
use App\Http\Requests\RecommendedPeopleRequest;
use App\Http\Resources\RecommendedUserResource;
use App\Services\DislikeService;
use App\Services\LikeService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function __construct(
        private UserService $userService,
        private LikeService $likeService,
        private DislikeService $dislikeService
    ) {}

    /**
     * Get recommended people for the authenticated user.
     *
     * @OA\Get(
     *     path="/api/people/recommended",
     *     summary="Get recommended users",
     *     tags={"People"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         description="Filter by gender",
     *         required=false,
     *         @OA\Schema(type="string", enum={"male","female","other"})
     *     ),
     *     @OA\Parameter(
     *         name="min_age",
     *         in="query",
     *         description="Minimum age",
     *         required=false,
     *         @OA\Schema(type="integer", example=18)
     *     ),
     *     @OA\Parameter(
     *         name="max_age",
     *         in="query",
     *         description="Maximum age",
     *         required=false,
     *         @OA\Schema(type="integer", example=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recommended users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="meta", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="per_page", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=150),
     *                     @OA\Property(property="last_page", type="integer", example=8)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function recommended(RecommendedPeopleRequest $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $filters = $request->only(['gender', 'min_age', 'max_age']);

        $users = $this->userService->getRecommendedUsers(
            $request->user(),
            $filters,
            $perPage
        );

        return ResponseFactory::success([
            'data' => RecommendedUserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Like a user.
     *
     * @OA\Post(
     *     path="/api/people/{id}/like",
     *     summary="Like a user",
     *     tags={"People"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID to like",
     *         required=true,
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully liked user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully liked user"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="liked_user_id", type="integer", example=123),
     *                 @OA\Property(property="liked_at", type="string", example="2025-11-04T12:45:30Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cannot like yourself"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=409, description="Already liked this user"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function like(Request $request, int $id): JsonResponse
    {
        try {
            $like = $this->likeService->likeUser($request->user()->id, $id);

            return ResponseFactory::success([
                'liked_user_id' => $like->liked_id,
                'liked_at' => $like->created_at->toIso8601String(),
            ], 'Successfully liked user');
        } catch (\Exception $e) {
            $statusCode = match ($e->getMessage()) {
                'User not found' => 404,
                'You have already liked this user' => 409,
                'You cannot like yourself' => 400,
                default => 400,
            };

            return ResponseFactory::error($e->getMessage(), null, $statusCode);
        }
    }

    /**
     * Dislike a user.
     *
     * @OA\Post(
     *     path="/api/people/{id}/dislike",
     *     summary="Dislike a user",
     *     tags={"People"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID to dislike",
     *         required=true,
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully disliked user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully disliked user"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="disliked_user_id", type="integer", example=123),
     *                 @OA\Property(property="disliked_at", type="string", example="2025-11-04T12:46:15Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cannot dislike yourself"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=409, description="Already disliked this user"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function dislike(Request $request, int $id): JsonResponse
    {
        try {
            $dislike = $this->dislikeService->dislikeUser($request->user()->id, $id);

            return ResponseFactory::success([
                'disliked_user_id' => $dislike->disliked_id,
                'disliked_at' => $dislike->created_at->toIso8601String(),
            ], 'Successfully disliked user');
        } catch (\Exception $e) {
            $statusCode = match ($e->getMessage()) {
                'User not found' => 404,
                'You have already disliked this user' => 409,
                'You cannot dislike yourself' => 400,
                default => 400,
            };

            return ResponseFactory::error($e->getMessage(), null, $statusCode);
        }
    }

    /**
     * Get users liked by the authenticated user.
     *
     * @OA\Get(
     *     path="/api/people/liked",
     *     summary="Get liked users",
     *     tags={"People"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liked users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="meta", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="per_page", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=45),
     *                     @OA\Property(property="last_page", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function liked(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);

        $users = $this->likeService->getLikedUsers($request->user()->id, $perPage);

        return ResponseFactory::success([
            'data' => RecommendedUserResource::collection($users->items()),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }
}
