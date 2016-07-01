@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-javascript")
<script src="{{ asset("highmaps/js/highmaps.js") }}"></script>
@endsection

@section("title", "空塵計")

@section("content")

<h1>空氣品質汙染指標 Open Data 下載</h1>
<h3>因為政府所提供的歷年空氣品質資料十分難以分析且格式錯亂，所以提供可自訂所需欄位的方式輸出已處理過後的資料。</h3>

{!! Form::open(array('route' => 'excel-export.export', 'method' => 'post')) !!}
<div>
    <h2>選擇年份</h2>
    <select name="year">
        @for($i = 2013; $i < 2016; $i++)
        <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>
<div>
    <h2>選擇欄位</h2>
    <input type="checkbox" id="pm25" name="outputdata[]" value="pm25" checked>
    <label for="pm25">PM2.5</label>
    <input type="checkbox" id="pm10" name="outputdata[]" value="pm10">
    <label for="pm10">PM10</label>
    <input type="checkbox" id="so2" name="outputdata[]" value="so2">
    <label for="so2">SO2</label>
    <input type="checkbox" id="co" name="outputdata[]" value="co">
    <label for="co">CO</label>
    <input type="checkbox" id="no2" name="outputdata[]" value="no2">
    <label for="no2">NO2</label>
    <input type="checkbox" id="windspeed" name="outputdata[]" value="wind">
    <label for="wind">風速和風向</label>
</div>
<div>
    <h2>選擇區域</h2>
    <input type="radio" name="area" value="">北部<br>
    <input type="radio" name="area" value="">中部<br>
    <input type="radio" name="area" value="">南部<br>
    <input type="radio" name="area" value="">花東<br>
</div>

<input type="submit" class="btn btn-success" value="下載">
{!! Form::close() !!}

@endsection
