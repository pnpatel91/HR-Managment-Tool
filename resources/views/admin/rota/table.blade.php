<table class="table table-striped custom-table datatable" id="table">
    <thead>
    <tr>
        <th>Scheduled Shift</th>
        @for($d = Carbon\Carbon::parse($startDate); $d->lte(Carbon\Carbon::parse($endDate)); $d->addDay())
        <th>{{$d->format('Y-m-d l')}}</th>
        @endfor
    </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>
                <div class="user-add-shedule-list">
                    <h2 class="table-avatar">
                        <a href="" class="avatar" tooltip="{{$user->name}}" flow="right"><img alt="" src="{{$user->getImageUrlAttribute($user->id)}}"></a>
                        <a href="">{{$user->name}} <span>{{$user->departments[0]->name}}</span></a>
                    </h2>
                </div>
            </td>
            @for($d = Carbon\Carbon::parse($startDate); $d->lte(Carbon\Carbon::parse($endDate)); $d->addDay())
            @php 
                $rotas = \App\Rota::with('branch')->where('user_id',$user->id)->where('start_date',$d)->get();
            @endphp
            <td>
                @if($rotas->count()!=0)
                    @foreach($rotas as $rota)
                    <div class="user-add-shedule-list">
                        <h2>
                            <div class="anchertag" data-toggle="modal" data-target="#edit_schedule" style="border:2px dashed #1eb53a">
                                @if (auth()->user()->can('edit rota'))
                                       <a href="{{ route('admin.rota.edit', ['rota' => $rota->id]) }}" class="float-right ml-2"  id="popup-modal-button"><span tooltip="Edit" flow="right"><i class="fas fa-edit"></i></span></a>
                                @endif
                                @if (auth()->user()->can('delete rota'))
                                       <form method="post" class="float-right delete-form-rota" action="{{route('admin.rota.destroy', ['rota' => $rota->id ])}}"><input type="hidden" name="_token" value="{{Session::token()}}"><input type="hidden" name="_method" value="delete"><button type="submit" class="close"  tooltip="Delete" flow="left"><i class="fas fa-trash"></i></button></form>
                                @endif
                                <span class="username-info m-b-10 mt-4">Start at - {{$rota->start_date}} {{Carbon\Carbon::parse($rota->start_time)->format('H:i')}}</span>
                                <span class="username-info m-b-10">End at - {{$rota->end_date}} {{Carbon\Carbon::parse($rota->end_time)->format('H:i')}}</span>
                                <?php 
                                $end_date_time=Carbon\Carbon::parse($rota->end_date.' '.$rota->end_time);
                                $start_date_time=Carbon\Carbon::parse($rota->start_date.' '.$rota->start_time);
                                $shift_time = $end_date_time->diff($start_date_time)->format('%h hrs %i mins');?>
                                <span class="username-info m-b-10">Brake Time : 
                                @if($rota->break_start_time!='')
                                    {{Carbon\Carbon::parse($rota->break_start_time)->format('g:ia').' to '.Carbon\Carbon::parse($rota->break_start_time)->addMinutes(intval($rota->break_time))->format('g:ia')}}
                                @else
                                    {{intval($rota->break_time).' minutes'}}

                                @endif
                                </span>
                                <span class="username-info m-b-10">({{$shift_time}})</span>
                                @if($rota->remotely_work=='No')
                                    <span class="username-info m-b-10">Branch - {{$rota->branch->name}}</span>
                                @else
                                    <span class="username-info m-b-10">Remotely Work</span>
                                @endif
                            </div>
                        </h2>
                    </div>
                    @endforeach
                @else
                    <div class="user-add-shedule-list">
                        @can('create rota')
                        <a href="{{ route('admin.rota.create_single_rota',['user_id' => $user->id,'date' => $d]) }}" class="anchertag" id="popup-modal-button-rota">
                            <span tooltip="create new rota." flow="right"><i class="fas fa-plus"></i></span>
                        </a>
                        @endcan
                    </div>
                @endif
            </td>
            @endfor
        </tr> 
        @endforeach
    </tbody>
</table>