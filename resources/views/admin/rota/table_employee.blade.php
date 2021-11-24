<table class="table table-striped custom-table datatable" id="table" width="100%">
    <thead>
    <tr>
        <th>Date</th>
        <th>Start Date & Time</th>
        <th>End Date & Time</th>
        <th>Break Time</th>
        <th>Location</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
        @for($d = Carbon\Carbon::parse($startDate); $d->lte(Carbon\Carbon::parse($endDate)); $d->addDay())
            @php 
                $rotas = \App\Rota::with('branch')->where('user_id',$user->id)->where('start_date',$d)->get();
            @endphp
        <tr>
            @if($rotas->count()!=0)
                @foreach($rotas as $rota)
                    <td>
                        <a href="{{ route('admin.rota.edit_employee', ['rota' => $rota->id]) }}" class="text-dark"  id="popup-modal-button">{{$d->format('Y-m-d l')}}</a>
                    </td>
                    <td>
                        {{$rota->start_date}} {{Carbon\Carbon::parse($rota->start_time)->format('H:i')}}
                    </td>
                    <td>
                        {{$rota->end_date}} {{Carbon\Carbon::parse($rota->end_time)->format('H:i')}}
                    </td>
                    <td>
                        @if($rota->break_start_time!='')
                            {{Carbon\Carbon::parse($rota->break_start_time)->format('g:ia').' to '.Carbon\Carbon::parse($rota->break_start_time)->addMinutes(intval($rota->break_time))->format('g:ia')}}
                        @else
                            {{intval($rota->break_time).' minutes'}}

                        @endif
                    </td>
                    <td class=" col-md-3">
                        @if($rota->remotely_work=='No')
                            <span class="username-info m-b-10">{{$rota->branch->name}} - {{$rota->branch->address}}, {{$rota->branch->city}}, {{$rota->branch->state}}, {{$rota->branch->postcode}}, {{$rota->branch->country}}</span>
                        @else
                            <span class="username-info m-b-10">Remotely Work</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.rota.edit_employee', ['rota' => $rota->id]) }}" class="float-left ml-2"  id="popup-modal-button"><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i> Edit Notes</span></a>
                    </td>
                    
                @endforeach
            @else
                <td>
                    {{$d->format('Y-m-d l')}}
                </td>
                <td></td><td></td><td>No Scheduled</td><td></td><td></td>
            @endif
        </tr> 
        @endfor
    </tbody>
</table>