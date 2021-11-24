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


class WikiBlogsController extends Controller
{
    function __construct()
    {
        $this->middleware('can:create Wiki Blog', ['only' => ['create', 'store']]);
        $this->middleware('can:edit Wiki Blog', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete Wiki Blog', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.wikiBlog.index');
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function datatables(Request $request)
    {

        if ($request->ajax() == true) {

            $data = wikiBlogs::with('category','parent');

            return Datatables::eloquent($data)
                ->addColumn('action', function ($data) {
                    
                    $html='';
                    if (auth()->user()->can('edit Wiki Blog')){
                        $html.= '<a href="'.  route('admin.wikiBlog.edit', ['wikiBlog' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                    }

                    if (auth()->user()->can('delete Wiki Blog')){
                        $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.wikiBlog.destroy', ['wikiBlog' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                    }

                    return $html; 
                })

                ->addColumn('status', function ($data) {
                        if($data->status=='Active'){ $class= 'text-success';$status= 'Active';}else{$class ='text-danger';$status= 'Inactive';}
                        return '<div class="dropdown action-label">
                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o '.$class.'"></i> '.$status.' </a>
                                <div class="dropdown-menu dropdown-menu-right" style="">
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',1); return false;"><i class="fa fa-dot-circle-o text-success"></i> Active</a>
                                    <a class="dropdown-item" href="#" onclick="funChangeStatus('.$data->id.',0); return false;"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</a>
                                </div>
                            </div>';
                    })

                ->addColumn('category', function ($data) {
                        return $data->category->name;
                    })

                ->rawColumns(['action','status','category'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $status = ['Active', 'Inactive'];
        $wikiCategories = wikiCategories::all();
        return view('admin.wikiBlog.create', compact("wikiCategories","status"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WikiBlogStoreRequest $request)
    {
        try {

            $wikiBlog = new wikiBlogs();
            $wikiBlog->title = $request->title;
            $wikiBlog->description = $request->description;
            $wikiBlog->category_id = $request->category_id;
            $wikiBlog->parent_id = $request->parent_id?$request->parent_id:null;
            $wikiBlog->status = 'Active';
            $wikiBlog->created_by = auth()->user()->id;
            $wikiBlog->updated_by = auth()->user()->id;
            $wikiBlog->save();

            //Session::flash('success', 'Wiki Blog was created successfully.');
            //return redirect()->route('wikiBlog.index');

            return response()->json([
                'success' => 'Wiki Blog was created successfully.' // for status 200
            ]);

        } catch (\Exception $exception) {

            DB::rollBack();

            //Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            /*return redirect()->back()->withInput($request->all());*/

            return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\wikiBlogs  $wikiBlogs
     * @return \Illuminate\Http\Response
     */
    public function show(wikiBlogs $wikiBlog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\wikiBlogs  $wikiBlogs
     * @return \Illuminate\Http\Response
     */
    public function edit(wikiBlogs $wikiBlog)
    {
        $status = ['Active', 'Inactive'];
        $wikiCategories = wikiCategories::all();
        return view('admin.wikiBlog.edit', compact('wikiBlog', 'wikiCategories', 'status'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\wikiBlogs  $wikiBlogs
     * @return \Illuminate\Http\Response
     */
    public function update(WikiBlogUpdateRequest $request, wikiBlogs $wikiBlog)
    {
        try {

            if (empty($wikiBlog)) {
                //Session::flash('failed', 'Wiki Blog Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'Wiki Blog update denied.' // for status 200
                ]);   
            }

            $wikiBlog->title = $request->title;
            $wikiBlog->description = $request->description;
            $wikiBlog->category_id = $request->category_id;
            $wikiBlog->parent_id = $request->parent_id;
            $wikiBlog->status = $request->status;
            $wikiBlog->updated_by = auth()->user()->id;
            $wikiBlog->save();

            //Session::flash('success', 'A Wiki Blog updated successfully.');
            //return redirect('admin/wikiBlog');

            return response()->json([
                'success' => 'Wiki Blog update successfully.' // for status 200
            ]);

        } catch (\Exception $exception) {

            DB::rollBack();

            //Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            /*return redirect()->back()->withInput($request->all());*/

            return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\wikiBlogs  $wikiBlogs
     * @return \Illuminate\Http\Response
     */
    public function destroy(wikiBlogs $wikiBlog)
    {
        // delete related children blog   
        $wikiBlog->children()->delete();

        // delete wiki blog
        $wikiBlog->delete();

        //return redirect('admin/wikiBlog')->with('delete', 'wiki blog deleted successfully.');
        return response()->json([
            'delete' => 'wiki blog & child blogs deleted successfully.' // for status 200
        ]);
    }

    /**
     * Display the specified category blog DropDown (For Ajax DropDown).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function get_blog_by_category( Request $request )
    {
          $this->validate( $request, [ 'id' => 'required' ] );
          $wikiBlogs = wikiBlogs::where('category_id', $request->id)->get();
          
          //you can handle output in different ways, I just use a custom filled array. you may pluck data and directly output your data.
          $output = [];
          foreach( $wikiBlogs as $wikiBlog )
          {
             $output[$wikiBlog->id] = $wikiBlog->title;
          }
          return $output;
    }

    /**
     * Datatables Ajax Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function change_status(Request $request)
    {
        try {

            $wikiBlogs = wikiBlogs::find($request->id);
            if (empty($wikiBlogs)) {
                //Session::flash('failed', 'Wiki Blogs Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'Wiki Blogs update denied.' // for status 200
                ]);   
            }

            if($request->status==0){
                $status='Inactive';
            }else{
                $status='Active';
            }

            $wikiBlogs->status = $status;
            $wikiBlogs->save();

            //Session::flash('success', 'A Wiki Blogs updated successfully.');
            //return redirect('admin/print_buttons');

            return response()->json([
                'success' => 'Wiki Blogs update successfully.' // for status 200
            ]);

        } catch (\Exception $exception) {

            DB::rollBack();

            //Session::flash('failed', $exception->getMessage() . ' ' . $exception->getLine());
            /*return redirect()->back()->withInput($request->all());*/

            return response()->json([
                'error' => $exception->getMessage() . ' ' . $exception->getLine() // for status 200
            ]);
        }
    }
}
