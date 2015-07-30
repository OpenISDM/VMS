<div class="form-group col-md-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, array('class' => 'form-control')) !!}
</div>

<div class="form-group  col-md-6">
    {!! Form::label('slug', 'Slug:') !!}
    {!! Form::text('slug', null, array('class' => 'form-control')) !!}
</div>

<div class="form-group  col-md-6">
    {!! Form::label('description', 'Project Description:') !!}
    {!! Form::textarea('description', null, array('class' => 'no-horiz-resize form-control')) !!}
</div>

<div class="form-group  col-md-6">
    {!! Form::label('is_ongoing', 'Project still ongoing?:') !!}
    {!! Form::checkbox('is_ongoing', 1, null, ['id' => 'is-ongoing']) !!}
</div>

<div class="form-group  col-md-3">
    {!! Form::label('start_date', 'Project start date:') !!}
    {!! Form::text('start_date', null, array('id' => 'startdatepicker', 'class' => 'form-control')) !!}
</div>

<div class="form-group  col-md-3">
    {!! Form::label('end_date', 'Project end date:') !!}
    {!! Form::text('end_date', null, array('id' => 'enddatepicker', 'class' => 'form-control')) !!}
</div>

<div class="form-group col-md-6">
    {!! Form::submit($submit_text, ['class'=>'btn btn-primary']) !!}
</div>
