<?php

namespace App\Http\Controllers\Admin;

use App\Rota_template;
use App\Rota;
use App\User;
use App\Branch;
use App\Holiday;

use App\Http\Controllers\Controller;
use App\Http\Requests\RotaTemplateStoreRequest;
use App\Http\Requests\RotaTemplateUpdateRequest;
use App\Http\Requests\RotaStoreByRotaTemplateRequest;
use App\Traits\UploadTrait;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use JeroenDesloovere\Distance\Distance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Notification;
use Mail;

class RotaTemplateController extends Controller
{
    function __construct()
    {
        $this->middleware('can:create rota_template', ['only' => ['create', 'store']]);
        $this->middleware('can:edit rota_template', ['only' => ['edit', 'update']]);
        $this->middleware('can:delete rota_template', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.rota_template.index');
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

            
            $model = Rota_template::with('creator','editor');
            
            return Datatables::eloquent($model)
                    ->addColumn('action', function (Rota_template $data) {
                        $html='';
                        if (auth()->user()->can('edit rota_template')){
                            $html.= '<a href="'.  route('admin.rota_template.edit', ['rota_template' => $data->id]) .'" class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></a>';
                        }

                        if (auth()->user()->can('view rota_template')){
                            $html.= '<a href="'.  route('admin.rota.create_bulk', ['rota_template' => $data->id]) .'" class="btn btn-warning btn-sm float-left mr-3"  id="popup-modal-button"><span tooltip="create rota" flow="left"><i class="fas fa-plus"></i></span></a>';
                        }

                        if (auth()->user()->can('delete rota_template')){
                            $html.= '<form method="post" class="float-left delete-form" action="'.  route('admin.rota_template.destroy', ['rota_template' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="delete"><button type="submit" class="btn btn-danger btn-sm"><span tooltip="Delete" flow="up"><i class="fas fa-trash"></i></span></button></form>';
                        }

                        $html.= '<form method="post" class="float-left ml-3 replicate-form" action="'. route('admin.rota_template.replicate', ['rota_template' => $data->id ]) .'"><input type="hidden" name="_token" value="'. Session::token() .'"><input type="hidden" name="_method" value="get"><button type="submit" class="btn btn-info btn-sm"><span tooltip="Replicate" flow="up"><i class="fas fa-clone"></i></span></button></form>';

                        return $html; 
                    })
                    ->addColumn('start_time', function (Rota_template $data) {
                        $status='<span class="text-success"><i class="fas fa-sign-in-alt"></i></span> In at';
                        return $status .' '. Carbon::createFromFormat('H:i:s',$data->start_at)->format('g:ia');
                    })

                    ->addColumn('end_time', function (Rota_template $data) {
                        $status='<span class="text-danger"><i class="fas fa-sign-out-alt"></i></span> Out at';
                        return $status .' '. Carbon::createFromFormat('H:i:s',$data->end_at)->format('g:ia');
                    })

                    
                    //->editColumn('break_time', '{{intval($break_time)}} minutes')
                    ->editColumn('break_time', function (Rota_template $data) {
                        if($data->break_start_at!=''){
                            return Carbon::parse($data->break_start_at)->format('g:ia').' to '.Carbon::parse($data->break_start_at)->addMinutes(intval($data->break_time))->format('g:ia');
                        }else{
                            return intval($data->break_time).' minutes'; 
                        }
                        
                    })
                    ->addColumn('creator', function (Rota_template $data) {
                        return '<img src="'.$data->creator->getImageUrlAttribute($data->creator->id).'" alt="user_id_'.$data->creator->id.'" class="profile-user-img-small img-circle"> '. $data->creator->name;
                    })

                    ->addColumn('editor', function (Rota_template $data) {
                        return '<img src="'.$data->editor->getImageUrlAttribute($data->editor->id).'" alt="Admin" class="profile-user-img-small img-circle"> '. $data->editor->name;
                    })
                    
                    ->rawColumns(['start_time', 'end_time', 'creator', 'editor', 'action'])

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
        $types = ['Day', 'Week', 'Month'];
        $day_list = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];
        return view('admin.rota_template.create', compact("types", "day_list", "over_time", "remotely_work"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request\RotaTemplateStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RotaTemplateStoreRequest $request)
    {
        try {

            $rota_template = new Rota_template();
            $rota_template->name = $request->name;
            $rota_template->start_at = $request->start_at;
            $rota_template->max_start_at = $request->max_start_at;
            $rota_template->end_at = $request->end_at;
            $rota_template->break_time = $request->break_time;
            $rota_template->break_start_at = $request->break_start_at;
            $rota_template->types = $request->types;
            $rota_template->day_list = json_encode($request->day_list);
            $rota_template->over_time = $request->over_time;
            $rota_template->remotely_work = $request->remotely_work;
            $rota_template->created_by = auth()->user()->id;
            $rota_template->updated_by = auth()->user()->id;
            $rota_template->save();

            //Session::flash('success', 'rota_template was created successfully.');
            //return redirect()->route('rota_template.index');

            return response()->json([
                'success' => 'rota template was created successfully.' // for status 200
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
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function show(Rota_template $rota_template)
    {
        return view('admin.rota_template.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function edit(Rota_template $rota_template)
    {
        $types = ['Day', 'Week', 'Month'];
        $day_list = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $over_time = ["Yes","No"];
        $remotely_work = ["Yes","No"];
        return view('admin.rota_template.edit', compact("rota_template", "types", "day_list", "over_time", "remotely_work"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rota_template $rota_template)
    {
        try {

            if (empty($rota_template)) {
                //Session::flash('failed', 'branch Update Denied');
                //return redirect()->back();
                return response()->json([
                    'error' => 'rota template update denied.' // for status 200
                ]);   
            }

            $rota_template->name = $request->name;
            $rota_template->start_at = $request->start_at;
            $rota_template->max_start_at = $request->max_start_at;
            $rota_template->end_at = $request->end_at;
            $rota_template->break_time = $request->break_time;
            $rota_template->break_start_at = $request->break_start_at;
            $rota_template->types = $request->types;
            $rota_template->day_list = json_encode($request->day_list);
            $rota_template->over_time = $request->over_time;
            $rota_template->remotely_work = $request->remotely_work;
            $rota_template->updated_by = auth()->user()->id;
            $rota_template->save();

            //Session::flash('success', 'A rota_template updated successfully.');
            //return redirect('admin/rota_template');

            return response()->json([
                'success' => 'rota template update successfully.' // for status 200
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
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rota_template $rota_template)
    {

        // delete rota_template
        $rota_template->delete();

        //return redirect('admin/rota_template')->with('delete', 'rota_template deleted successfully.');
        return response()->json([
            'delete' => 'rota template deleted successfully.' // for status 200
        ]);
    }

    /**
     * get data of the specified resource.
     *
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function get_rota_template(Request $request)
    {
        return Rota_template::find($request->id)->toJSON();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rota_template  $rota_template
     * @return \Illuminate\Http\Response
     */
    public function replicate(Rota_template $rota_template)
    {
        $newPost = $rota_template->replicate();
        $newPost->created_at = Carbon::now();
        $newPost->save();

        return response()->json([
            'success' => 'rota template replicate successfully.' // for status 200
        ]);

    }
    
}
