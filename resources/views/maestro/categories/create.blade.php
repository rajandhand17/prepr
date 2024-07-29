@extends('maestro.layouts.default')
@section('title', 'Create Category')
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
              <h1>Create Category</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Create Category</li>
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
              <h3 class="card-title">Create Category</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <label class="radio-inline"><input type="radio" value="category" name="catType"  checked="checked"> Category</label>
                      <label class="radio-inline" style="padding-left:10px"><input type="radio" value="subcategory" name="catType"> Sub Category</label>
                  </div>
                  <div class="clearfix"></div>
                  </div>
                  {!!Form::open(array('method'=>'POST','route'=>['category.store']))!!}
                    
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group {{($errors->has('parent_id')) ? 'has-error' : ''}}"
                          id="categoryBox">
                         {!! Form::label('parent_id', 'Parent Category', ['class' => 'control-label']) !!}
                         {!! Form::select('parent_id', $category_list, old('parent_id'), ['class' => 'form-control']) !!}
                         <span class="help-block">{{ $errors->first('parent_id')}}</span>
                     </div>
                    </div>
                    <div class="clearfix"></div>
                    </div>

                    <div class="row">
                        @if($languages->count() > 0)
                          @foreach($languages as $single)
                            @php
                              $titleColumName = \App\Helpers\Maestro\UtilityHelper::getColumName($single->iso, 'title');
                              $titleLableName = \App\Helpers\Maestro\UtilityHelper::getLabelName($single->name, 'Category Name');
                            @endphp
                            <div class="col-md-6">
                              <div class="form-group">
                                {!! Form::label($titleColumName, $titleLableName, ['class' => 'control-label']) !!}
                                {!! Form::text($titleColumName,old($titleColumName), ['class' => 'form-control','required' => 'required'])
                                !!}
                              </div>
                            </div>
                          @endforeach
                        @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>component</label>
                                <select name="components[]" class="select2" multiple="multiple" data-placeholder="Select component" style="width: 100%;" required>
                                      <option value="lab" selected>Lab</option>
                                      <option value="challenge" selected>Challenge</option>
                                      <option value="project" selected>Project</option>
                                </select>
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

  /***
   * category box toggle show & hide on change radio button
   */
  $('input[type=radio][name=catType]').change(function () {
      $('#categoryBox').toggle();
      $("#parent_id").val("");
  });

  $(document).ready(function () {

      $('#components').select2();

  });
</script>
@endsection