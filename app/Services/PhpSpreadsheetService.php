<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;
use App\Models\masterallowdeducts as modelMasterAllowDeducts;

class PhpSpreadsheetService
{

    /**
     * テンプレートシート中のタグを置き換える
     * @param $sheet
     * @param $billData
     * @return void
     */
    protected function replaceTags($sheet, $billData)
    {
        $rows = range(1, $sheet->getHighestRow());
        $cols = range('A', $sheet->getHighestColumn());
        foreach($rows as $row) {
            foreach($cols as $col) {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();
                if(substr($value, 0, 2) == '##') {
                    $tag = substr($value, 2);
                    if(isset($billData[$tag])) {
                        $cell->setValue($billData[$tag]);
                    }
                }
            }
        }
    }

    /**
     * 明細開始セルを検索する
     * @param $sheet
     * @return string
     */
    protected function searchStartCell($sheet)
    {
        $rows = range(1, $sheet->getHighestRow());
        $cols = range('A', $sheet->getHighestColumn());
        foreach($rows as $row) {
            foreach($cols as $col) {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();
                if($value == '$$begin') {
                    return $col . $row;
                }
            }
        }
        return '';
    }

    /**
     * 請求書エクスポート
     * @param $templateFile
     * @param $billData    請求情報
     * @param $billDetails   請求明細
     * @return response
     */
    public function exportBill($templateFile, $billData, $billDetails)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $sheet = $spreadsheet->getSheet(0);
        // search and replace bill tags
        $this->replaceTags($sheet, $billData);
        // search start cell
        $sStartCell = $this->searchStartCell($sheet);
        // insert rows
        $nStartRow = substr($sStartCell, 1);
        $no = 1;
        foreach($billDetails as $billDetail) {
            $nRow = $nStartRow + $no - 1;
            $sheet->insertNewRowBefore($nStartRow + $no); // 最初に行を挿入
            $sheet->getcell('B' . $nRow)->setValue($no);
            $sheet->getcell('C' . $nRow)->setValue($billDetail->title);
            $sheet->getcell('E' . $nRow)->setValue(number_format($billDetail->unit_price));
            $sheet->getcell('F' . $nRow)->setValue(number_format($billDetail->quantity, 2));
            $sheet->getcell('G' . $nRow)->setValue($billDetail->unit);
            $sheet->getcell('H' . $nRow)->setValue(number_format($billDetail->amount));
            $sheet->getcell('I' . $nRow)->setValue(number_format($billDetail->tax));
            $no++;
        }
        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save('/var/www/html/storage/data/bill.xlsx');
        $fileName = '請求書' . $billData['bill_no'] . '.xlsx';
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="' . $fileName . '"');
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download('/var/www/html/storage/data/bill.xlsx', $fileName, $headers);
    }

    /**
     * 請求明細エクスポート
     * @param $templateFile
     * @param $clientInfo
     * @param $employeeSalarys
     * @return response
     */
    public function exportBillDetails($templateFile, $clientInfo, $employeeSalarys)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $sheet = $spreadsheet->getSheet(0);
        $sheet->getcell('A1')->setValue($clientInfo['cl_name']);
        $sheet->getcell('A2')->setValue($clientInfo['cl_place_name']);

        $no = 1;
        foreach($employeeSalarys as $employeeSalary) {
            $nRow = $no + 4;
            $sheet->getcell('A' . $nRow)->setValue($employeeSalary->wrk_date);
            $sheet->getcell('B' . $nRow)->setValue($employeeSalary->employee->empl_name_last . ' ' . $employeeSalary->employee->empl_name_first);
            $sheet->getcell('C' . $nRow)->setValue($employeeSalary->wrk_work_start);
            $sheet->getcell('D' . $nRow)->setValue($employeeSalary->wrk_work_end);
            $sheet->getcell('E' . $nRow)->setValue($employeeSalary->wrk_work_hours);
            $sheet->getcell('F' . $nRow)->setValue($employeeSalary->billhour);
            $sheet->getcell('G' . $nRow)->setValue($employeeSalary->wrk_bill);
            $sheet->getcell('H' . $nRow)->setValue($employeeSalary->wt_bill_item_name);
            $no++;
        }
        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save('/var/www/html/storage/data/billdetail.xlsx');
        $fileName = '請求明細.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download('/var/www/html/storage/data/billdetail.xlsx', $fileName, $headers);
    }

    /**
     * 給与エクスポート
     * @param $templateFile
     * @param $SalaryInfo
     * @param $Salaries
     * @return response
     */
    public function exportSalaries($templateFile, $SalaryInfo, $Salaries)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $sheet = $spreadsheet->getSheet(0);

        $nRow = 2;
        foreach($Salaries as $Salary) {
            $sheet->getcell('A' . $nRow)->setValue($Salary->work_year);
            $sheet->getcell('B' . $nRow)->setValue($Salary->work_month);
            $sheet->getcell('C' . $nRow)->setValue($Salary->employee->empl_cd);
            $sheet->getcell('D' . $nRow)->setValue($Salary->employee->empl_name_last . ' ' . $Salary->employee->empl_name_first);
            $sheet->getcell('E' . $nRow)->setValue($Salary->work_amount);
            $sheet->getcell('F' . $nRow)->setValue($Salary->allow_amount);
            $sheet->getcell('G' . $nRow)->setValue($Salary->deduct_amount);
            $sheet->getcell('H' . $nRow)->setValue($Salary->transport);
            $sheet->getcell('I' . $nRow)->setValue($Salary->pay_amount);
            $nRow++;
        }
        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save(storage_path('data/salary.xlsx'));
        $fileName = '給与' . $SalaryInfo['work_year'] . substr('00' . $SalaryInfo['work_month'], -2) . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download(storage_path('data/salary.xlsx'), $fileName, $headers);
    }

    /**
     * 給与明細エクスポート
     * @param $templateFile
     * @param $SalaryInfo
     * @param $Salaries
     * @param $SalaryDetails
     * @return response
     */
    public function exportSalaryDetails($templateFile, $SalaryInfo, $Salaries, $SalaryDetails)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $sheet = $spreadsheet->getSheet(0);
        // ヘッダー
        $sheet->getcell('A1')->setValue($SalaryInfo['work_year'].'年'.$SalaryInfo['work_year'].'月');

        $saveEmployeeId = null;
        $noSalary = 0;
        $nRow = 2;
        foreach($SalaryDetails as $SalaryDetail)
        {
            if($SalaryDetail->employee_id != $saveEmployeeId)
            {
                // 従業員情報
                $Salary = $Salaries->get($noSalary++);
                if((! $Salary) || ($Salary->employee_id != $SalaryDetail->employee_id))
                {
                    throw new \Exception('給与情報が不正です。');
                }
                // 従業員の手当控除情報
                $EmployeeAllowDeducts = modelEmployeeAllowDeduct::with('masterallowdeduct')
                    ->where('employee_id', $SalaryDetail->employee_id)
                    ->where('work_year', $SalaryInfo['work_year'])
                    ->where('work_month', $SalaryInfo['work_month'])
                    ->orderBy('mad_deduct')
                    ->orderBy('mad_cd')
                    ->get();

                $sheet->getcell('A' . $nRow)->setValue('従業員番号');
                $sheet->getcell('A' . $nRow + 1)->setValue($Salary->employee->empl_cd);

                $sheet->getcell('B' . $nRow)->setValue('氏名');
                $sheet->getcell('B' . $nRow + 1)->setValue($Salary->employee->empl_name_last . ' ' . $Salary->employee->empl_name_first);

                $sheet->getcell('C' . $nRow)->setValue('勤怠額');
                $sheet->getcell('C' . $nRow + 1)->setValue($Salary->work_amount);
                $sheet->getcell('D' . $nRow)->setValue('交通費');
                $sheet->getcell('D' . $nRow + 1)->setValue($Salary->transport);

                $colNo = 5;
                foreach($EmployeeAllowDeducts as $AllowDeduct)
                {
                    $sheet->getcell([$colNo, $nRow])->setValue($AllowDeduct->mad_name);
                    $sheet->getcell([$colNo, $nRow + 1])->setValue($AllowDeduct->amount);
                    $colNo++;
                }

                $sheet->getcell([$colNo, $nRow])->setValue('支給額');
                $sheet->getcell([$colNo, $nRow + 1])->setValue($Salary->pay_amount);
                $colNo++;

                $nRow += 2;

                $sheet->getcell('A' . $nRow)->setValue('日付');
                $sheet->getcell('B' . $nRow)->setValue('有給');
                $sheet->getcell('C' . $nRow)->setValue('開始');
                $sheet->getcell('D' . $nRow)->setValue('終了');
                $sheet->getcell('E' . $nRow)->setValue('勤務時間');
                $sheet->getcell('F' . $nRow)->setValue('時給');
                $sheet->getcell('G' . $nRow)->setValue('金額');
                $sheet->getcell('H' . $nRow)->setValue('作業種別');
                $nRow++;

                $saveEmployeeId = $SalaryDetail->employee_id;
            }
            
            $sheet->getcell('A' . $nRow)->setValue($SalaryDetail->wrk_date);
            $sheet->getcell('B' . $nRow)->setValue($SalaryDetail->leave ? '有給': '');
            $sheet->getcell('C' . $nRow)->setValue($SalaryDetail->wrk_work_start);
            $sheet->getcell('D' . $nRow)->setValue($SalaryDetail->wrk_work_end);
            $sheet->getcell('E' . $nRow)->setValue($SalaryDetail->wrk_work_hours);
            $sheet->getcell('F' . $nRow)->setValue($SalaryDetail->payhour);
            $sheet->getcell('G' . $nRow)->setValue($SalaryDetail->wrk_pay);
            $sheet->getcell('H' . $nRow)->setValue($SalaryDetail->wt_bill_item_name);
            $nRow++;
        }

        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save(storage_path('data/salary_detail.xlsx'));
        $fileName = '給与明細' . $SalaryInfo['work_year'] . substr('00' . $SalaryInfo['work_month'], -2) . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download(storage_path('data/salary_detail.xlsx'), $fileName, $headers);
    }
}