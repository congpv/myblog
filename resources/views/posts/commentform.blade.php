<div class="form-group" >
  {!! Form::label('comment',' ')  !!}
  {!! Form::textarea('comment',null,['placeholder'=>'Add a comment...','class' => 'form-control showbutton', 'rows'=>'1']) !!}    
</div>
<div class="form-group buttoncomment">
    {!! Form::button('Comment',['class'=>'send btn btn-danger', 'id'=>'btn-comment']) !!}
</div>