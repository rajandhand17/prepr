@extends('maestro.layouts.default')
@section('title', 'Edit Category')
@section('content')
<style type="text/css">
  #categoryBox {
    display: none;
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Category</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Edit Category</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- SELECT2 EXAMPLE -->
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title">Edit Category</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::model($parentCategory,array('method'=>'PUT','route'=>array('category.update',$parentCategory->id)))!!}

        <div class="row">
          <div class="col-md-12">
            @if(!$category)
            <div class="form-group {{($errors->has('parent_id')) ? 'has-error' : ''}}">
              {!! Form::label('parent_id', 'Parent Category', ['class' => 'control-label']) !!}
              {!! Form::select('parent_id', $category_list, old('parent_id'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('parent_id')}}</span>
            </div>
            @endif
          </div>
          <div class="clearfix"></div>
        </div>

        <div class="row">
          @if($languages->count() > 0)
            @foreach($languages as $single)
          @php
          if ($single->iso == 'en') {
            $lableName = 'English Name';
            $inputName = 'title';
          } else {
            $columName = $single->iso;
            $lableName = $single->title . ' Name';
            if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
              $columName = str_replace(' ', '_', $columName);
            }
            if ($columName == trim($columName) && strpos($columName, '-') !== false) {
              $columName = str_replace('-', '_', $columName);
            }
            $inputName = $columName . '_title';
          }
          @endphp
          <div class="col-md-6">
            <div class="form-group {{($errors->has('name')) ? 'has-error' : ''}}">
              {!! Form::label($inputName, $lableName, ['class' => 'control-label']) !!}
              {!! Form::text($inputName,null, ['class' => 'form-control','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first($inputName)}}</span>
            </div>
          </div>
          @endforeach
          @endif
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <div class="form-group">
                {!! Form::label('components', 'components', ['class' => 'control-label']) !!}
                <select class="form-control" , multiple , id="components" name="components[]" required>
                  @if(!empty($components))
                  @foreach($components as $k=>$v){
                  @if( $k == $v)
                  <option value={{$k}} selected>{{ ucfirst($v) }} </option>
                  @else
                  <option value={{$k}}>{{ ucfirst($k) }} </option>
                  @endif
                  }
                  @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a class="btn btn-danger mr-1" href="{{ route('category.index') }}"><i class="icon-cross2"></i> Cancel</a>
            </div>
          </div>
        </div>
        {!!Form::close()!!}
      </div>
      <!-- /.row -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
  </div>
  <!-- /.container-fluid -->
</section>
<!-- /.content -->
@stop

@section('scripts')
<script type="text/javascript">
  $('input[type=radio][name=catType]').change(function () {
      $('#categoryBox').toggle();
      $("#parent_id").val("");
  });
  $(document).ready(function () {
      $('#components').select2();
  });;
</script>
@endsection