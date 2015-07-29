<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name') !!}
</div>
 
<div class="form-group">
    {!! Form::label('slug', 'Slug:') !!}
    {!! Form::text('slug') !!}
</div>
 
<div class="form-group">
    {!! Form::label('description', 'Process Description:') !!}
    {!! Form::textarea('description') !!}
</div>

<div class="form-group">
    {!! Form::label('is_ongoing', 'Process still ongoing?:') !!}
    {!! Form::checkbox('is_ongoing') !!}
</div>

<div class="form-group">
    {!! Form::label('start_date', 'Project start date:') !!}
    {!! Form::text('start_date', null, array('id' => 'startdatepicker')) !!}
</div>

<div class="form-group">
    {!! Form::label('end_date', 'Project end date:') !!}
    {!! Form::text('end_date', null, array('id' => 'enddatepicker')) !!}
</div>
 
<div class="form-group">
    {!! Form::submit($submit_text, ['class'=>'btn primary']) !!}
</div>
