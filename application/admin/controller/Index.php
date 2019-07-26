<?php
namespace app\admin\controller;
/**
 * 
 */
use think\Controller;
use Request;
use think\Db;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
class Index extends Common
{
	public function index()
	{
		return $this->fetch('index/index');
	}
    public function add(){
        $helper = new Sample();
        $file = request()->file('my_file');
        $info = $file->move( 'static/xlsl');
        $filename=$info->getSaveName();
        $inputFileName='static/xlsl/'.$filename;
        $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        foreach ($sheetData as $key => $value){
            $name=$value['A'];
            $password=$value['B'];
            $arr=Db::query("select * from user where user_name='$name'");
            if (empty($arr)){
                Db::query("insert into user(user_name,password) values ('$name','$password')");
            }
        }
        $filename=str_replace('\\','/',$filename);
        echo $filename;
        unset($info);
        unlink('./static/xlsl/'.$filename);
        echo "Ok";
    }
}