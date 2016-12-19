{{-- @extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-javascript")
@endsection

@section("title", "空塵計")

@section("content") --}}
	<p> HI {{ $name }} </p>
	<p> {{ $content }}</p>
{{-- @endsection

@section("head-javascript")
@endsection --}}