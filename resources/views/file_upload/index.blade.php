@extends('layouts.master')

@section('title', '空塵計')
    
@section('content')

<div class="col-md-12" style="text-align: center;">
    <a href="{{ route('file-upload.single') }}"><button class="btn btn-primary" style="margin: 20px 0;">單檔上傳</button></a>
    <a href="{{ route('file-upload.batch') }}"><button class="btn btn-primary" style="margin: 20px 0;">批次處理</button></a>
</div>

@endsection
