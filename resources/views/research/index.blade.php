@extends('layouts.master')

@section('title', '空塵計')

@section('content')
<div class="col-md-12">
    <a href="{{ route('research.average') }}"><button class="btn btn-primary" style="margin: 20px 0;">平均值比較</button></a>
    {{-- <a href="{{ route('research.') }}"><button class="btn btn-primary" style="margin: 20px 0;"></button></a> --}}
</div>
@endsection