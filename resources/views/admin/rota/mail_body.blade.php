<style type="text/css">
    tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
</style>
<table  align="left" border="0" width="100%" style="font-size: 15px;border-collapse: collapse;margin-bottom: 20px;">
    <tbody>
    <tr align="center" style="background-color: #000;color: white !important;height: 30px;" border="0">
        <td>Date</td>
        <td>Start Date & Time</td>
        <td>End Date & Time</td>
        <td>Break Time</td>
        <td>Location</td>
    </tr>
        @for($d = Carbon\Carbon::parse($startDate); $d->lte(Carbon\Carbon::parse($endDate)); $d->addDay())
            @php 
                $rotas = \App\Rota::with('branch')->where('user_id',$user->id)->where('start_date',$d)->get();
            @endphp
        
            @if($rotas->count()!=0)
                @foreach($rotas as $rota)
                <tr align="center" style="height: 30px;">
                    <td>
                        {{$d->format('Y-m-d D')}}
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
                            {{$rota->branch->name}}
                        @else
                            Remotely Work
                        @endif
                    </td>
                </tr>     
                @endforeach
            @else
            <tr align="center" style="height: 30px;">
                <td>
                    {{$d->format('Y-m-d D')}}
                </td>
                <td colspan="4">No Scheduled</td>
            </tr> 
            @endif
        
        @endfor
    </tbody>
</table>
<br><br>