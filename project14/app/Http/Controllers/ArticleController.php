<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Type;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::all();
        $types = Type::all();

        return view('article.index', ['articles' => $articles, 'types' => $types]);
    }

    public function indexAjax()
    {
        $articles = Article::with('type_id')->sortable()->get();

        $articles_array = array(
            'articles' => $articles
        );

        $json_response = response()->json($articles_array);
        return $json_response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('article.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreArticleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = new Article;
        $article->title = $request->article_title;
        $article->description = $request->article_description;
        $article->type_id = $request->type_id;

        $article->save();

        return redirect()->route('article.index');
    }

    public function storeAjax(Request $request)
    {
        $article = new Article;
        $article->title = $request->article_title;
        $article->description = $request->article_description;
        $article->type_id = $request->type_id;

        $article->save();

        $article_array = [
            'successMsg' => "Article created successfully",
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleDescription' => $article->description,
            'typeId' => $article->type_id
        ];

        $json_response = response()->json($article_array);

        return $json_response;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view("article.show", ['article' => $article]);
    }

    public function showAjax(Article $article)
    {
        $article_array = [
            'successMsg' => "Article retrieved successfully",
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleDescription' => $article->description,
            'typeId' => $article->type_id
        ];

        $json_response = response()->json($article_array);

        return $json_response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateArticleRequest  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    public function updateAjax(Request $request, Article $article)
    {
        $article->title = $request->article_title;
        $article->description = $request->article_description;
        $article->type_id = $request->type_id;

        $article->save();

        $article_array = [
            'successMsg' => "Article updated successfully",
            'articleId' => $article->id,
            'articleTitle' => $article->title,
            'articleDescription' => $article->description,
            'typeId' => $article->type_id
        ];

        $json_response = response()->json($article_array);

        return $json_response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route("article.index");
    }

    public function destroyAjax(Article $article)
    {
        $article->delete();

        $success_array = [
            'successMsg' => "Article deleted successfully" . $article->id,
        ];

        $json_response = response()->json($success_array);

        return $json_response;
    }

    public function searchAjax(Request $request)
    {

        $searchValue = $request->searchValue;

        $articles = Article::query()
            ->where('title', 'like', "%{$searchValue}%")
            ->orWhere('description', 'like', "%{$searchValue}%")
            ->orWhere('type', 'like', "%{$searchValue}%")
            ->get();

        if (count($articles) > 0) {
            $articles_array = array(
                'articles' => $articles
            );
        } else {
            $articles_array = array(
                'errorMessage' => 'No articles found'
            );
        }



        $json_response = response()->json($articles_array);

        return $json_response;
    }
}
