@extends('layouts.master')

@section('title', '空塵記')

@section('head-css')
<link rel="stylesheet" href="{{ asset('css/upload.css') }}">
@stop
    
@section('content')

<div class="col-md-12" style="text-align: center;">
    <h2>～～請上傳已轉換好的json檔案～～</h2>
    {!! Form::open(array('route' => 'file-upload.upload', 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}
        <label class="file">
            <input type="file" name="file_json" onchange="this.parentNode.setAttribute('title', this.value.replace(/^.*[\\/]/, ''))" />
        </label>
        <input type="submit" class="btn btn-success" value="送出" style="width: 150px">
    {!! Form::close() !!}
</div>

@stop

@section('page-javascript')
<script>
// $('input[type=file]').change(function () {
//     var value = $('input[type=file]').val();
//     if (value == '') {
//         $('.file-value').text('選擇檔案...');
//     } else {
//         $('.file-value').text(value);    
//     }
// });
</script>
@stop