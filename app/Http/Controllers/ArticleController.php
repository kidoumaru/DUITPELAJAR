<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::visibleToUsers()->latest('published_at')->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        abort_unless(
            $article->status->value === 'published' &&
            ($article->published_at === null || $article->published_at <= now()),
            404
        );

        return view('articles.show', compact('article'));
    }
}
