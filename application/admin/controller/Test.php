<?php
namespace app\admin\controller;
/**
 *
 */
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use \tp5er\easyExcel;
use think\excel\PHPExcel;
use think\Db;
//use think\Controller;
//use \PhpOffice\PhpSpreadsheet\IOFactory;
class Test extends Common
{
	public function test()
	{
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

            return;
        }
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        // Add some data

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'user_name')
            ->setCellValue('C1', 'password');
//            ->setCellValue('D2', 'world!');

        // Miscellaneous glyphs, UTF-8
        $arr=Db::query("select * from user");
        foreach ($arr as $key => $value){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+2), $value['id'])
                ->setCellValue('B'.($key+2), $value['user_name'])
                ->setCellValue('C'.($key+2), $value['password']);
        }
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
		}
        function join(){
	    //获取表格的大小，限制上传表格的大小5M
            $file_size = $_FILES['myfile']['size'];
            if ($file_size > 5 * 1024 * 1024) {
                $this->error('文件大小不能超过5M');
                exit();
            }
            //限制上传表格类型
            $fileExtendName = substr(strrchr($_FILES['myfile']["name"], '.'), 1);
            //application/vnd.ms-excel  为xls文件类型
            if ($fileExtendName != 'xls') {
                $this->error('必须为excel表格，且必须为xls格式！');
                exit();
            }
            if (is_uploaded_file($_FILES['myfile']['tmp_name'])) {
                // 有Xls和Xlsx格式两种
                $objReader = IOFactory::createReader('Xls');
                $filename = $_FILES['myfile']['tmp_name'];
                $objPHPExcel = $objReader->load($filename);  //$filename可以是上传的表格，或者是指定的表格
                $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
                $highestRow = $sheet->getHighestRow();       // 取得总行数
                // $highestColumn = $sheet->getHighestColumn();   // 取得总列数
                //定义$usersExits，循环表格的时候，找出已存在的用户
                $usersExits = [];
                //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
                for ($j = 2; $j <= $highestRow; $j++) {
                    $data[$j - 2] = [
                        'admin_username' => $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue(),
                        'admin_password' => $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue(),
                        'create_time' => time()
                    ];
                    //看下用户名是否存在。将存在的用户名保存在数组里。
                    $userExist = db('admin')->where('admin_username', $data[$j - 2]['admin_username'])->find();
                    if ($userExist) {
                                array_push($usersExits, $data[$j - 2]['admin_username']);
                    }
                }
                //halt($usersExits);

                //如果有已存在的用户名，就不插入数据库了。
                if ($usersExits != []) {
                    //把数组变成字符串，向前端输出。
                    $c = implode(" / ", $usersExits);
                    $this->error('Excel中以下用户名已存在:' . $c, "/backend/admin/create", '', 20);
                    exit();
                }

                //halt($data);
                //插入数据库
                $res = db('user')->insertAll($data);
//                if ($res) {
//                    $this->success('上传成功！', '/backend/admin', '', 1);
//                }
            }
	    }
	}