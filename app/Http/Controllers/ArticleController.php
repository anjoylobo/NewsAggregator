<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::paginate(10);
        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        return response()->json($article);
    }

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

        $articles = $query->paginate(10); // Paginate results
        return response()->json($articles);
    }
}
