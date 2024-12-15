<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;

class UserPreferenceController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter 'Bearer' [space] and then your token."
     * )
     */
    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set user preferences",
     *     description="Set preferred sources, categories, and authors for the user.",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences saved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="preferences", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     * )
     */
    public function setPreferences(Request $request)
    {
        $request->validate([
            'preferred_sources' => 'array',
            'preferred_categories' => 'array',
            'preferred_authors' => 'array',
        ]);

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->only('preferred_sources', 'preferred_categories', 'preferred_authors')
        );

        return response()->json(['message' => 'Preferences saved successfully', 'preferences' => $preferences]);
    }

    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get user preferences",
     *     description="Retrieve the preferences of the currently logged-in user.",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="preferences", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="No preferences found")
     * )
     */

    public function getPreferences()
    {
        $preferences = UserPreference::where('user_id', Auth::id())->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return response()->json(['preferences' => $preferences]);
    }

    /**
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Get personalized feed",
     *     description="Retrieve articles based on user preferences, with optional keyword filtering.",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to filter articles by title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="No preferences found")
     * )
     */

    public function getPersonalizedFeed(Request $request)
    {
        $userPreferences = UserPreference::where('user_id', Auth::id())->first();

        if (!$userPreferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        $query = Article::query();

        if ($userPreferences->preferred_sources) {
            $query->whereIn('source', $userPreferences->preferred_sources);
        }

        if ($userPreferences->preferred_categories) {
            $query->whereIn('category', $userPreferences->preferred_categories);
        }

        if ($userPreferences->preferred_authors) {
            $query->where(function ($q) use ($userPreferences) {
                $q->whereIn('author', $userPreferences->preferred_authors)
                  ->orWhereNull('author');
            });
        }

        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%')
                ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        $articles = $query->paginate(10);
        return response()->json($articles);
    }
}
