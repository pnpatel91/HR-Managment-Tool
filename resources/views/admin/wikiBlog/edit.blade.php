@extends('admin.layouts.popup')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="error_message">
            @include('message.alert')
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="card-title">Edit Wiki Blog</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.wikiBlog.update', ['wikiBlog' => $wikiBlog->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" id="title" value="{{$wikiBlog->title}}" class="form-control" required autocomplete="title" autofocus maxlength="60">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description" name="description" class="form-control" required maxlength="5" autofocus>{{$wikiBlog->description}}</textarea>  
                        </div>

                        <div class="form-group">
                            <label>Wiki Categories</label>
                            <select class="form-control select2" id="category_id" name="category_id" required autocomplete="category_id" onchange="get_blog_by_category(this.value)">
                                <option value=""></option>
                                @foreach ($wikiCategories as $wikiCategory)
                                    <option value="{{ $wikiCategory->id }}" @if($wikiBlog->category_id == $wikiCategory->id) selected @endif>{{ $wikiCategory->name }}</option>
                                @endforeach
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
                        </div>

                        <div class="form-group">
                            <label>Parent</label>
                            <select class="form-control select2" id="parent_id" name="parent_id" autocomplete="parent_id">
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control select2" id="status" name="status" required autocomplete="status">
                                @foreach ($status as $status)
                                    <option value="{{ $status }}" @if($wikiBlog->status==$status) selected @endif>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="" class="btn btn-secondary"  data-dismiss="modal">Close</a>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //CKEDITOR for description
    /*CKEDITOR.replace( 'description',
          {
             filebrowserBrowseUrl : '{{ asset('/') }}plugins/ckfinder/ckfinder.html',
             filebrowserImageBrowseUrl : '{{ asset('/') }}/plugins/ckfinder/ckfinder.html?Type=Images',
             filebrowserFlashBrowseUrl : '{{ asset('/') }}/plugins/ckfinder/ckfinder.html?Type=Flash',
             filebrowserUploadUrl : '{{ asset('/') }}/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
             filebrowserImageUploadUrl : '{{ asset('/') }}/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
             filebrowserFlashUploadUrl : '{{ asset('/') }}/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
          } 
    );*/
    CKEDITOR.replace('description', {
      extraPlugins: 'uploadimage,image',
      height: 300,

      // Upload images to a CKFinder connector (note that the response type is set to JSON).
      uploadUrl: '{{ asset('/') }}/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',

      // Configure your file manager integration. This example uses CKFinder 3 for PHP.
      filebrowserBrowseUrl: '{{ asset('/') }}plugins/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl: '{{ asset('/') }}plugins/ckfinder/ckfinder.html?type=Images',
      filebrowserUploadUrl: '{{ asset('/') }}/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
      filebrowserImageUploadUrl: '{{ asset('/') }}/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',

      // The following options are not necessary and are used here for presentation purposes only.
      // They configure the Styles drop-down list and widgets to use classes.

      stylesSet: [{
          name: 'Narrow image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-narrow'
          }
        },
        {
          name: 'Wide image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-wide'
          }
        }
      ],

      // Load the default contents.css file plus customizations for this sample.
      contentsCss: [
        'http://cdn.ckeditor.com/4.16.2/full-all/contents.css',
        'https://ckeditor.com/docs/ckeditor4/4.16.2/examples/assets/css/widgetstyles.css'
      ],

      // Configure the Enhanced Image plugin to use classes instead of styles and to disable the
      // resizer (because image size is controlled by widget styles or the image takes maximum
      // 100% of the editor width).
      image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
      image2_disableResizer: true,
      removeButtons: 'PasteFromWord'
    });

    // jQuery Validation
    $(function(){
        $('#popup-form').validate(
        {
            rules:{
              
            }
        }); //valdate end
    }); //function end

    

    get_blog_by_category({{$wikiBlog->category_id}});

    function get_blog_by_category(category_id) {
        $("#pageloader").fadeIn();
        $("#parent_id option").remove();
        
        var id = category_id;
        
        $.ajax({
          url : '{{ route('admin.wikiBlog.ajax.get_blog_by_category') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id
            },
          type: 'post',
          dataType: 'json',
          success: function( result )
          {
            $('#parent_id').append($('<option>', {value:'', text:'Parent Blog'}));
            $.each( result, function(k, v) {
                if(k!={{$wikiBlog->id}}){
                    if(k=={{$wikiBlog->parent_id?$wikiBlog->parent_id:0}}){
                        $('#parent_id').append('<option selected value='+k+'>'+v+'</option>');
                    }else{
                        $('#parent_id').append('<option value='+k+'>'+v+'</option>'); 
                    }
                    
                }
            });

            $("#pageloader").hide();
          }
        });
    }


    $("#category_id").select2({
      placeholder: "select a wiki category",
      allowClear: false
    });

    $("#parent_id").select2({
      placeholder: "select a parent blog",
      allowClear: false
    });
</script>
@endsection