<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use ConstantObjects;
use App\Models\Thread;
use App\Models\Post;

trait UserObjectTraits{

    public function select_user_comments($find_all, $id, $request)
    {
        if ($find_all) {
            $posts = Post::join('threads', 'threads.id', '=', 'posts.thread_id')
            ->withUser($id)
            ->where('threads.deleted_at', '=', null)
            ->withType(['post', 'comment'])
            ->ordered('latest_created')
            ->select('posts.*')
            ->paginate(config('preference.posts_per_page'));
        } else {
            $queryid = 'UserComment.'
            .$id
            .(is_numeric($request->page)? 'P'.$request->page:'P1');

            $posts = Cache::remember($queryid, 10, function () use($request, $id) {
                return $posts = Post::join('threads', 'threads.id', '=', 'posts.thread_id')
                ->userOnly($id)
                ->where('threads.deleted_at', '=', null)
                ->isPublic()
                ->inPublicChannel()
                ->withType(['post', 'comment'])
                ->withFolded()
                ->ordered('latest_created')
                ->select('posts.*')
                ->paginate(config('preference.posts_per_page'))
                ->appends($request->only('page'));
            });
        }

        $posts->load('simpleThread');
        return $posts;
    }

    public function select_user_threads($find_all, $is_book, $id, $request) {

        if ($find_all) {
            $query = Thread::with('tags','author','last_post')
            ->withUser($id);

            $data = $this->query_filter($query, $is_book);
        } else {
            $queryid = 'User'.($is_book ? 'Book.' : 'Thread.')
            .$id
            .(is_numeric($request->page)? 'P'.$request->page:'P1');

            $data = Cache::remember($queryid, 10, function () use($is_book, $request, $id) {
                $query = Thread::with('tags','author','last_post')
                ->withUser($id)
                ->isPublic()
                ->inPublicChannel()
                ->withAnonymous('none_anonymous_only');

                $query = $this->query_filter($query, $is_book)
                ->appends($request->only('page'));

                return $query;
            });
        }

        return $data;
    }

    private function query_filter($query, $is_book) {
        if ($is_book) {
            $query->withType('book')->ordered('latest_add_component');
        } else {
            $query->withoutType('book')->ordered();
        }

        return $query->paginate(config('preference.threads_per_page'));
    }
}
