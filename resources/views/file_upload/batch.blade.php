@extends('layouts.master')

@section('csrf-token')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@stop

@section('title', '空塵計')

@section('content')

<style>
    .box {
        width: 47%;
        padding: 10px 10px 20px;
        margin: 0 10px 30px;
        float: left;
        position: relative;
        border: 1px solid #000;
        border-radius: 5px;
    }
    .box > h3 {
        text-align: center;
    }
    .table-out {
        width: 100%;
        border: 1px solid #AAA;
    }
    .table-out th {
        width: 50%;
        font-size: 16pt;
        text-align: center;
        border: 1px solid #AAA;
    }
    .scroll {
        height: 300px;
        overflow: auto;
    }
    .table-body {
        width: 100%;
    }
    .table-body td {
        padding: 1px 0; 
        width: 50%;
        text-align: center;
        border: 1px solid #AAA;
    }
    .table-body td > button {
        margin: 1px 5px;
    }
</style>

<div class="col-md-12">
    <h2 style="text-align: center;">歷史資料上傳批次作業</h2>
    <div class="box">
        {{-- 檔案列表 --}}
        <h3>檔案列表</h3>
        <table class="table-out">
            <thead>
                <tr>
                    <th>檔案名稱</th>
                    <th>功能</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="scroll">
                            <table class="table-body">
                                <tbody id="list"></tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="box">
        {{-- 確定要執行列表 --}}
        <h3>確定上傳</h3>
        <button class="btn btn-warning" onclick="batchStart();" style="position: absolute;right: 10px;top: 30px;">開始作業</button>
        <table class="table-out">
            <thead>
                <tr>
                    <th>檔案名稱</th>
                    <th>取消上傳</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="scroll">
                            <table class="table-body">
                                <tbody id="ready"></tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
{{-- <h1 style="position: absolute;">完成度．．．XX%</h1> --}}
@endsection

@section('page-javascript')
<script>
    
    function addFileList(file_name) {
        var html = '<tr id="'+file_name+'">';
        html = html + '<td>'+file_name+'.json</td>';
        html = html + '<td>';
        html = html + '<button class="btn btn-success" onClick="removeFileList(\''+file_name+'\')"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>確定</button>';
        html = html + '<button class="btn btn-danger" onClick="deleteFile(\''+file_name+'\')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>刪除檔案</button>';
        html = html + '</td>';
        html = html + '</tr>';

        $('#list').append(html);
    }

    function removeFileList(file_name) {
        $('#'+file_name).remove();
        addFileReady(file_name);
    }

    function addFileReady(file_name) {
        var html = '<tr id="'+file_name+'">';
        html = html + '<td>'+file_name+'.json</td>';
        html = html + '<td>';
        html = html + '<button class="btn btn-danger" onClick="removeFileReady(\''+file_name+'\')"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消上傳</button>';
        html = html + '</td>';
        html = html + '</tr>';

        $('#ready').append(html);
    }

    function removeFileReady(file_name) {
        $('#'+file_name).remove();
        addFileList(file_name);
    }

    function deleteFile(file_name) {
        if (confirm('確定要刪除\t'+file_name+'.json\t?')) {
            var post_data = {
                _token: $('meta[name=csrf-token]').attr('content'),
                file: file_name,
            };
            $.ajax({
                type: 'POST',
                url: '{{ route('file-upload.delete') }}',
                data: post_data,
                success: function (msg) {
                    alert(msg);
                    $('#'+file_name).remove();
                },
                error: function (e) {
                    alert('發生錯誤。請聯絡管理員。');
                }
            });
        }
    }

    function batchStart() {
        if (!confirm("開始後請勿關閉此視窗，等待作業完成。")) {
            return ;
        }

        f();
        var timer = setInterval(function () {
            f();
            if ($('#ready > tr').length == 0) clearInterval(timer) ;
        }, 25000);
    }

    function f() {
        file = $('#ready > tr')[0].id;
        console.log('call f()'+file);
        var post_data = {
            _token: $('meta[name=csrf-token]').attr('content'),
            file: file,
        };

        $.ajax({
            type: 'POST',
            url: '{{ route('file-batch.start') }}',
            data: post_data,
            async: false,
            success: function (msg) {
                console.log(msg);
                removeFileReady(file);
            },
            error: function (e) {
                alert('發生錯誤。請聯絡管理員。');
            }
        });
    }

    $(document).ready(function () {
        var files = {!! json_encode($files) !!};
        files.forEach( function(element, index) {
            addFileList(element);
        });
    });

</script>
@stop
