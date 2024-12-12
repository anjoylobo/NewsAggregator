<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Article;

class UserPreferenceController extends Controller
{
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

    public function getPreferences()
    {
        $preferences = UserPreference::where('user_id', Auth::id())->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return response()->json(['preferences' => $preferences]);
    }

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
        $query->whereIn('author', $userPreferences->preferred_authors);
    }

    // Optionally, merge with additional search criteria
    if ($request->has('keyword')) {
        $query->where('title', 'like', '%' . $request->keyword . '%')
              ->orWhere('description', 'like', '%' . $request->keyword . '%');
    }

    $articles = $query->paginate(10); // Paginate results
    return response()->json($articles);
}
}
