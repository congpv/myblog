<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Post;
use App\Comment;
use App\Like;
use Auth;
use Session;

class PostController extends Controller
{
    public function __construct() {
        $this->middleware(['auth', 'clearance']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderby('id', 'desc')->paginate(10); //show only 5 items at a time in descending order

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validating title and body field
        $this->validate($request, [
            'title'=>'required|max:100',
            'body' =>'required',
            ]);

        $title = $request['title'];
        $body = $request['body'];

        $post = Post::create($request->only('title', 'body'));

    //Display a successful message upon save
        return redirect()->route('posts.index')
            ->with('flash_message', 'Article, '. $post->title.' created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $post = Post::findOrFail($id); //Find post of id = $id
        $comments = Comment::select('comments.*', 'users.name')
        ->leftjoin('users', 'comments.user_id', '=', 'users.id')
        ->where(['comments.post_id'=>$id])
        ->orderBy('comments.updated_at', 'desc')
        ->paginate(5);
        if($request->ajax()) {
            return view('posts.commentlist', ['comments' => $comments])->render();  
        }
        $like = Like::where(['user_id'=>\Auth::id(), 'post_id'=>$id])->first();
        $numlike = Like::where(['post_id'=>$id, 'like'=>1])->count();
        $numdislike = Like::where(['post_id'=>$id, 'like'=>0])->count();
        
        return view ('posts.show', compact('post'))->with(['comments'=>$comments, 'like'=>$like, 'numlike'=>$numlike, 'numdislike'=>$numdislike]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'=>'required|max:100',
            'body'=>'required',
        ]);

        $post = Post::findOrFail($id);
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->save();

        return redirect()->route('posts.show', 
            $post->id)->with('flash_message', 'Article, '. $post->title.' updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')
            ->with('flash_message', 'Article successfully deleted');
    }

    public function storecomment(Request $request) {
        if ($request->ajax()) {
            $data    = $request->get('data');
            $response = ['code'=>'', 'data'=>'', 'message'=>''];
            //// validate
            $rules = array(
                'user_id'       => 'required|integer',
                'post_id'      => 'required|integer',
                'comment_text' => 'required|string'
            );
            $datastore['user_id'] =  \Auth::id();
            $datastore['post_id'] =  $data['post_id'];
            $datastore['comment_text'] =  $data['comment'];
            
            
            $validator = Validator::make($datastore, $rules);

            if ($validator->fails()) {
                $response['code'] = 500;
                $response['message'] = 'Error post comment';
            } else {
                // store
                $comment = new Comment;
                $comment->user_id = $datastore['user_id'] ;
                $comment->post_id = $datastore['post_id'] ;
                $comment->comment_text = $datastore['comment_text'] ;
                if($comment->save()) {
                    $comments = Comment::select('comments.*', 'users.name')
                    ->leftjoin('users', 'comments.user_id', '=', 'users.id')
                    ->where(['comments.post_id'=>$datastore['post_id']])
                    ->orderBy('comments.updated_at', 'desc')
                    ->paginate(5);
                    $returnHTML = view('posts.commentlist', ['comments' => $comments])->render();  
                    $response['code'] = 200;
                    $response['message'] = 'Success post comment';
                    $response['data'] = $returnHTML;
                }
            }
            return $response;
        }
    }

    public function storelike(Request $request) {
        if ($request->ajax()) {            
            $data    = $request->get('data');
            $response = ['code'=>'', 'data'=>'', 'message'=>''];
            //// validate
            $rules = array(
                'user_id'       => 'required|integer',
                'post_id'      => 'required|integer',
                'like' => 'required|boolean'
            );
            $datastore['user_id'] =  \Auth::id();
            $datastore['post_id'] =  $data['post_id'];
            $datastore['like'] =  intval($data['like']);
            $validator = Validator::make($datastore, $rules);

            if ($validator->fails()) {
                $response['code'] = 500;
                $response['message'] = 'Error post like';
            } else {
                //Find clicked before
                $like = Like::where(['user_id'=>$datastore['user_id'], 'post_id'=>$datastore['post_id']])->first();
                if(!$like) {
                    // store
                    $like = new Like;
                    $like->user_id = $datastore['user_id'] ;
                    $like->post_id = $datastore['post_id'] ;
                    $like->like = $datastore['like'] ;
                    if($like->save()) {                    
                        $response['code'] = 200;
                        $response['message'] = 'Success post like';
                        $response['data'] = $like->like;
                    }
                } else {
                    $response['code'] = 201;
                    $response['message'] = 'Like of user for article is existed';
                    $response['data'] = $like;
                }
                
            }
            return $response;
        }
    }
}
