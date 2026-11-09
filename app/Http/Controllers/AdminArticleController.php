<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::with('author')->latest()->paginate(15);

        return view('admin.articles.index', compact('articles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'content.required' => 'Konten artikel wajib diisi.',
            'category.required' => 'Kategori artikel wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')
                ->store('article-thumbnails', 'public');
        }

        $baseSlug = Str::slug($validated['title']);
        $slug = $this->ensureUniqueSlug($baseSlug);

        Article::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'category' => $validated['category'],
            'status' => ArticleStatus::from($validated['status']),
            'published_at' => $validated['published_at'] ?? null,
            'thumbnail' => $thumbnailPath,
            'author_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:published,draft'],
            'published_at' => ['nullable', 'date'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')
                ->store('article-thumbnails', 'public');
        }

        $validated['status'] = ArticleStatus::from($validated['status']);

        $article->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    private function ensureUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
