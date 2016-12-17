<? php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use Carbon\Carbon;

class TestLog extends Command
{
    // 命令名稱
    protected $signature = 'email:Log';

    // 說明文字
    protected $description = '[測試] Log 檔案';

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        // 檔案紀錄在 storage/test.log
        $log_file_path = storage_path('email.log');

        // 記錄當時的時間
        $log_info = [
            'date'=>Carbon::now('Asia/Taipei')->toDateTimeString();
        ];

        // 記錄 JSON 字串
        $log_info_json = json_encode($log_info) . "\r\n";

        // 記錄 Log
        File::append($log_file_path, $log_info_json);
    }
}
