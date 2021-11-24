<?php

namespace App\Http\Controllers\admin;

use App\wikiBlogs;
use App\wikiCategories;
use App\Http\Controllers\Controller;
use App\Http\Requests\WikiBlogStoreRequest;
use App\Http\Requests\WikiBlogUpdateRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;


class WikiBlogsViewController extends Controller
{
    function __construct()
    {
        $this->middleware('can:create Wiki Blog View', ['only' => ['create', 'store']]);
        $this->middleware('can:edit Wiki Blog View', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete Wiki Blog View', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $categories = wikiCategories::with(['wikiBlogs' => function ($query) {
                                                            $query->where('parent_id', null)->where('status', 'Active');
                                                },'wikiBlogs.allChildren'])->where('status', 'Active')->whereHas('users', function($q) use ($user_id) { $q->where('user_id', $user_id); })->get();
        //dd($categories);
        return view('admin.wikiBlogView.index',compact('categories'));
    }

    /**
     * Display blog details (For Ajax).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function get_blog_details( Request $request )
    {
          $this->validate( $request, [ 'id' => 'required' ] );
          $wikiBlog = wikiBlogs::where('id', $request->id)->first();

          return $wikiBlog;
    }


    /**
     * search Sidebar (For Ajax).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function search( Request $request )
    {
          $this->validate( $request, [ 'search' => 'required' ] );
          $wikiBlog = wikiBlogs::where('title', 'like', "%{$request->search}%")->orWhere('description', 'like', "%{$request->search}%")->get();

          $html = '<div class="list-group">';
          if(count($wikiBlog)>0){
            foreach ($wikiBlog as $value) {
                $title = $value->title;
                $title = str_replace($request->search,'<strong class="text-light">'.$request->search.'</strong>',$title);
                  $html .= '<a href="#" onclick="linkclickable('.$value->id.')" class="list-group-item" ><div class="search-title">'.$title.'</div></a>';
            }
          }else{
            $html .= '<a href="#" class="list-group-item" ><div class="search-title">No Result Found!!</div></a>';
          }
          

          $html .= '</div>';
          return $html;
    }
}
