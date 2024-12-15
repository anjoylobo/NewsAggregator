<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
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
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Get a list of articles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No articles available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No articles available")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $articles = Article::paginate(10);

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles available'], 200);
        }

        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Get a single article by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example="Article not found")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['errors' => 'Article not found'], 404);
        }

        return response()->json($article);
    }

    /**
     * @OA\Post(
     *     path="/api/articles/search",
     *     tags={"Articles"},
     *     summary="Search for articles",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="keyword", type="string", description="Search keyword"),
     *             @OA\Property(property="date", type="string", format="date", description="Publication date"),
     *             @OA\Property(property="category", type="string", description="Category of the article"),
     *             @OA\Property(property="source", type="string", description="Source of the article")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"keyword": {"The keyword must be a string."}})
     *         )
     *     )
     * )
     */

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'string|nullable',
            'date' => 'date|nullable',
            'category' => 'string|nullable',
            'source' => 'string|nullable',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Article::query();

        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        $articles = $query->paginate(10);
        return response()->json($articles);
    }
}
