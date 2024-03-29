<?php

namespace App\Http\Controllers;

use App\Post;
use App\Category;
use Illuminate\Support\Facades\Session;

class RssController extends Controller
{

    public function index($type = 'sitemap')
    {
        if ($type == 'sitemap') {
            $posts = Post::byPublished()->byLanguage(request()->get('lang'))->byApproved()->limit(get_buzzy_config('RSSSitemapPostLimit', 500))->get();
            $categories = Category::byLanguage(request()->query('lang'))->get();

            return  response()->view('pages.sitemap', compact('posts', 'categories'))->header('Content-Type', 'application/xml');
        }

        if ($type == 'googlenews') {
            $posts = Post::byPublished()->byLanguage(request()->get('lang'))->byApproved()->limit(get_buzzy_config('GoogleNewsPostLimit', 500))->get();

            return response()->view('pages.googlenews', compact('posts'))->header('Content-Type', 'application/xml');
        }

        $posts = $this->getdata($type);

        if (!$posts) {
            Session::flash('error.message',  trans('index.emptyplace'));
            return redirect()->back();
        }

        return  response()->view('pages.rss', compact('posts'))->header('Content-Type', 'application/xml');
    }

    public function fbinstant()
    {
        $posts = Post::with('user')->where('type', '!=', 'quiz')->byPublished()->byLanguage(request()->get('lang'))->byApproved()->limit(get_buzzy_config('FIAPostLimit', 150))->get();

        return  response()->view('pages.instant-rss', compact('posts'))->header('Content-Type', 'application/xml');
    }

    public function getdata($type)
    {
        if ($type == 'index' || $type == 'feed') {
            $posts = Post::byPublished()->byLanguage(request()->get('lang'))->byApproved()->limit(get_buzzy_config('RSSFeedPostLimit', 500))->get();
        } elseif ($type == 'top-today') {
            $posts    = Post::forHome()->getStats('one_day_stats', 'DESC', 10)->byPublished()->byLanguage(request()->get('lang'))->byApproved()->get();
        } else {
            $category = Category::with('children', 'allChildrens')->where("name_slug", $type)->first();

            if (!$category) {
                return [];
            }

            $childIds = $category->allChildrens->pluck('id')->prepend($category->id)->all();

            $posts = Post::byCategories($childIds)
                ->byPublished()->byLanguage(request()->get('lang'))->byApproved()->take(50)->get();
        }

        return $posts;
    }

    public function json($type)
    {
        if ((int)$type > 0) {
            $category = Category::with('children', 'allChildrens')->find(intval($type));

            if (!isset($category)) {
                return response()->json([]);
            }

            $childIds = $category->allChildrens->pluck('id')->prepend($category->id)->all();

            $posts = Post::byCategories($childIds)
                ->byPublished()->byLanguage(request()->get('lang'))->byApproved()->take(15)->get();
        } else {
            $posts = $this->getdata($type);
        }

        $result = [];
        if ($posts) {
            foreach ($posts as $key => $post) {
                $result[] = array(
                    'title' => $post->title,
                    'description' => $post->body,
                    'thumb' => makepreview($post->thumb, 'b', 'posts'),
                    'link' => $post->post_link,
                    'published_at' => $post->published_at,
                    'user' => $post->user ? array(
                        'username' => $post->user->username,
                        'name' => $post->user->name,
                        'avatar' => makepreview($post->user->icon, 'b', 'users'),
                        'link' =>  $post->user->profile_link,
                    ) : null,
                    'views' => [
                        'today' => (int) $post->one_day_stats,
                        'weekly' => (int) $post->seven_days_stats,
                        'monthly' => (int) $post->thirty_days_stats,
                        'alltime' => (int) $post->all_time_stats,
                    ],
                    'shares' => $post->shared,
                );
            }
        }

        return response()->json($result);
    }
}
