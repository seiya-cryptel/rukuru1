<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Consts\AppConsts;
use App\Traits\rukuruUtilities;

use App\Models\masterallowdeducts as modelMasterAllowDeducts;
use App\Models\clientworktypes as modelClientWorkTypes;
use App\Models\employeeworks as modelEmployeeWorks;
use App\Models\employeeallowdeduct as modelEmployeeAllowDeduct;

use App\Models\clients;
use App\Models\clientplaces;
use App\Models\employees;

class PhpSpreadsheetService
{
    use rukuruUtilities;

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

            $nCol = 2;
            $sheet->getcell([$nCol++, $nRow])->setValue($no);

            $sheet->mergeCells('C' .  $nRow . ':' . 'D' . $nRow);
            $sheet->getcell([$nCol, $nRow])->setValue($billDetail->title);
            $nCol += 2;

            $sheet->getStyle([$nCol, $nRow])->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail->unit_price);

            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail->quantity_string);

            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail->unit);

            $sheet->getStyle([$nCol, $nRow])->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail->amount);

            $sheet->getStyle([$nCol, $nRow])->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail->tax);
            $no++;
        }
        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        $writer->save(storage_path('data/bill.xlsx'));
        $fileName = '請求書' . $billData['cl_name'] . date('Y-m-d H:i') . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download(storage_path('data/bill.xlsx'), $fileName, $headers);
    }

    /**
     * 請求明細エクスポート
     * @param $templateFile
     * @param $clientInfo
     * @param $billDetails
     * @return response
     */
    public function exportBillDetails($templateFile, $clientInfo, $billDetails)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $nCol = 2;
        $nRow = 1;
        $sheet = $spreadsheet->getSheet(0);
        $sheet->getcell([$nCol, $nRow++])->setValue($clientInfo['cl_name']);
        $sheet->getcell([$nCol, $nRow++])->setValue($clientInfo['cl_place_name']);
        $sheet->getcell([$nCol, $nRow++])->setValue($clientInfo['work_year'] . '年' . $clientInfo['work_month'] . '月');
        $sheet->getcell([$nCol, $nRow++])->setValue($clientInfo['first_date'] . '〜' . $clientInfo['last_date']);

        $nRow = 7;
        foreach($billDetails as $billDetail) {
            $nCol = 1;
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail['empl_name']);
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail['summary_name']);
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail['billhour']);
            $sheet->getStyle([$nCol, $nRow])->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail['unit_price']);
            $sheet->getStyle([$nCol, $nRow])->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getcell([$nCol++, $nRow])->setValue($billDetail['bill_amount']);
            $nRow++;
        }

        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save(storage_path('data/billdetail.xlsx'));
        $fileName = '請求明細 ' . $clientInfo['cl_name'] . date('Y-m-d H_i') . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download(storage_path('data/billdetail.xlsx'), $fileName, $headers);
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

        $nRow = 1;
        foreach($Salaries as $Salary) {
            $nCol = 1;
            $sheet->getcell([$nCol++, $nRow])->setValue('年');
            $sheet->getcell([$nCol++, $nRow])->setValue('月');
            $sheet->getcell([$nCol++, $nRow])->setValue('従業員コード');
            $sheet->getcell([$nCol++, $nRow])->setValue('氏名');
            $sheet->getcell([$nCol++, $nRow])->setValue('勤怠');
            $sheet->getcell([$nCol++, $nRow])->setValue('交通費');
            $sheet->getcell([$nCol++, $nRow])->setValue('手当');
            $sheet->getcell([$nCol++, $nRow])->setValue('控除');
            $sheet->getcell([$nCol++, $nRow])->setValue('支給額');

            $nCol = 1;
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->work_year);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->work_month);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->employee->empl_cd);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->employee->empl_name_last . ' ' . $Salary->employee->empl_name_first);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->work_amount);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->transport);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->allow_amount);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->deduct_amount * -1);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->pay_amount);

            // 手当控除
            $employee_id = $Salary->employee_id;
            $EmployeeAllowDeducts = modelEmployeeAllowDeduct::where('employee_id', $employee_id)
                ->where('work_year', $Salary->work_year)
                ->where('work_month', $Salary->work_month)
                ->orderBy('mad_deduct')
                ->orderBy('mad_cd')
                ->get();

            $nCol++;
            foreach($EmployeeAllowDeducts as $AllowDeduct)
            {
                // 交通費は除外
                if($AllowDeduct->mad_cd == AppConsts::MAD_CD_TRANSPORT) {
                    continue;
                }
                $sheet->getcell([$nCol, $nRow])->setValue($AllowDeduct->mad_name);
                $amount = $AllowDeduct->mad_deduct ? ($AllowDeduct->amount * -1) : $AllowDeduct->amount;
                $sheet->getcell([$nCol, ($nRow + 1)])->setValue($amount);
                $nCol++;
            }
            $nRow += 3;
        }
        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save(storage_path('data/salary.xlsx'));
        $fileName = '給与' . $SalaryInfo['work_year'] . substr('00' . $SalaryInfo['work_month'], -2) . '_' . date('YmdHi') . '.xlsx';
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
     * @return response
     */
    public function exportSalaryDetails($templateFile, $SalaryInfo, $Salaries)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $sheet = $spreadsheet->getSheet(0);

        // 対象期間
        $dtFirstDate = strtotime($SalaryInfo['work_year'] . '-' . $SalaryInfo['work_month'] . '-01');
        $dtLastDate = strtotime('-1 day', strtotime('+1 month', $dtFirstDate));
        $workDateFirst = date('Y-m-d', $dtFirstDate);
        $workDateLast = date('Y-m-d', $dtLastDate);

        $nRow = 1;
        foreach($Salaries as $Salary) {
            $nCol = 1;
            $sheet->getcell([$nCol++, $nRow])->setValue('年');
            $sheet->getcell([$nCol++, $nRow])->setValue('月');
            $sheet->getcell([$nCol++, $nRow])->setValue('従業員コード');
            $sheet->getcell([$nCol++, $nRow])->setValue('氏名');
            $sheet->getcell([$nCol++, $nRow])->setValue('勤怠');
            $sheet->getcell([$nCol++, $nRow])->setValue('交通費');
            $sheet->getcell([$nCol++, $nRow])->setValue('手当');
            $sheet->getcell([$nCol++, $nRow])->setValue('控除');
            $sheet->getcell([$nCol++, $nRow])->setValue('支給額');

            $nCol = 1;
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->work_year);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->work_month);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->employee->empl_cd);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->employee->empl_name_last . ' ' . $Salary->employee->empl_name_first);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->work_amount);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->transport);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->allow_amount);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->deduct_amount * -1);
            $sheet->getcell([$nCol++, ($nRow + 1)])->setValue($Salary->pay_amount);

            // 手当控除
            $employee_id = $Salary->employee_id;
            $EmployeeAllowDeducts = modelEmployeeAllowDeduct::where('employee_id', $employee_id)
                ->where('work_year', $Salary->work_year)
                ->where('work_month', $Salary->work_month)
                ->orderBy('mad_deduct')
                ->orderBy('mad_cd')
                ->get();

            $nCol++;
            foreach($EmployeeAllowDeducts as $AllowDeduct)
            {
                // 交通費は除外
                if($AllowDeduct->mad_cd == AppConsts::MAD_CD_TRANSPORT) {
                    continue;
                }
                $sheet->getcell([$nCol, $nRow])->setValue($AllowDeduct->mad_name);
                $amount = $AllowDeduct->mad_deduct ? ($AllowDeduct->amount * -1) : $AllowDeduct->amount;
                $sheet->getcell([$nCol, ($nRow + 1)])->setValue($amount);
                $nCol++;
            }
            $nRow += 2;

            // 項目名
            $nCol = 1;
            $sheet->getcell([$nCol++, $nRow])->setValue('日付');
            $sheet->getcell([$nCol++, $nRow])->setValue('作業名');
            $sheet->getcell([$nCol++, $nRow])->setValue('打刻開始');
            $sheet->getcell([$nCol++, $nRow])->setValue('打刻終了');
            $sheet->getcell([$nCol++, $nRow])->setValue('勤務開始');
            $sheet->getcell([$nCol++, $nRow])->setValue('勤務終了');
            $sheet->getcell([$nCol++, $nRow])->setValue('休憩時間');
            $sheet->getcell([$nCol++, $nRow])->setValue('勤務時間');
            $sheet->getcell([$nCol++, $nRow])->setValue('時給');
            $sheet->getcell([$nCol++, $nRow])->setValue('金額');
            $nRow++;

            $employeeWorks = modelEmployeeWorks::where('employee_id', $employee_id)
                ->whereBetween('wrk_date', [$workDateFirst, $workDateLast])
                ->orderBy('wrk_date')
                ->orderBy('wrk_seq')
                ->get();
            foreach($employeeWorks as $employeeWork)
            {
                if($employeeWork->leave) {
                    $nCol = 1;
                    $sheet->getcell([$nCol++, $nRow])->setValue($employeeWork->wrk_date);
                    $sheet->getcell([$nCol++, $nRow])->setValue('有休');
                    $nCol += 7;
                    $sheet->getcell([$nCol++, $nRow])->setValue($this->rukuruUtilMoneyValue($Salary->paid_leave_pay, 0));
                }
                else
                {
                    $clientWorkType = modelClientWorkTypes::where('client_id', $employeeWork->client_id)
                    ->where('clientplace_id', $employeeWork->clientplace_id)
                    ->where('wt_cd', $employeeWork->wt_cd)
                    ->first();

                    $nCol = 1;
                    $sheet->getcell([$nCol++, $nRow])->setValue($employeeWork->wrk_date);
                    $sheet->getcell([$nCol++, $nRow])->setValue($clientWorkType->wt_name);
                    $sheet->getcell([$nCol++, $nRow])->setValue($employeeWork->wrk_log_start);
                    $sheet->getcell([$nCol++, $nRow])->setValue($employeeWork->wrk_log_end);
                    $sheet->getcell([$nCol++, $nRow])->setValue(date('md Hi', strtotime($employeeWork->wrk_work_start)));
                    $sheet->getcell([$nCol++, $nRow])->setValue(date('md Hi', strtotime($employeeWork->wrk_work_end)));
                    $sheet->getcell([$nCol++, $nRow])->setValue($employeeWork->wrk_break);
                    $sheet->getcell([$nCol++, $nRow])->setValue($employeeWork->wrk_work_hours);
                    $sheet->getcell([$nCol++, $nRow])->setValue($this->rukuruUtilMoneyValue($employeeWork->payhour, 0));
                    $sheet->getcell([$nCol++, $nRow])->setValue($this->rukuruUtilMoneyValue($employeeWork->wrk_pay, 0));
                }
                $nRow++;
            }
            $nRow++;
        }

        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save(storage_path('data/salary_detail.xlsx'));
        $fileName = '給与明細' . $SalaryInfo['work_year'] . substr('00' . $SalaryInfo['work_month'], -2) . '_' . date('YmdHi') . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download(storage_path('data/salary_detail.xlsx'), $fileName, $headers);
    }

    /**
     * 勤怠明細エクスポート
     * @param $templateFile
     * @param $KintaiInfo
     * @param $Employeeworks
     * @return response
     */
    public function exportKintaiDetail($templateFile, $KintaiInfo, $Employeeworks)
    {
        // load template file
        $spreadsheet = (new XlsxReader())->load($templateFile);
        // get first sheet
        $sheet = $spreadsheet->getSheet(0);

        $clientInfo = '';
        if($KintaiInfo['client_id'])
        {
            $client = clients::find($KintaiInfo['client_id']);
            if($client)
            {
                $clientInfo = $client->cl_cd . ' ' . $client->cl_name;
            }
        }

        // 検索条件
        $sheet->getcell('A3')->setValue('検索条件');
        $sheet->getcell('A4')->setValue('期間');
        $sheet->getcell('B4')->setValue($KintaiInfo['date_from'] . '〜' . $KintaiInfo['date_to']);
        $sheet->getcell('A5')->setValue('顧客');
        $sheet->getcell('B5')->setValue($clientInfo);
        $sheet->getcell('A6')->setValue('従業員コード');
        $sheet->getcell('B6')->setValue($KintaiInfo['empl_cd_from'] . '〜' . $KintaiInfo['empl_cd_to']);

        $nRow = 8;
        // ヘッダー
        $sheet->getcell('A' . $nRow)->setValue('従業員');
        $sheet->getcell('B' . $nRow)->setValue('日付');
        $sheet->getcell('C' . $nRow)->setValue('有休');
        $sheet->getcell('D' . $nRow)->setValue('顧客');
        $sheet->getcell('E' . $nRow)->setValue('部門');
        $sheet->getcell('F' . $nRow)->setValue('開始打刻');
        $sheet->getcell('G' . $nRow)->setValue('終了打刻');
        $sheet->getcell('H' . $nRow)->setValue('開始時間');
        $sheet->getcell('I' . $nRow)->setValue('終了時間');
        $sheet->getcell('J' . $nRow)->setValue('休憩時間');
        $sheet->getcell('K' . $nRow)->setValue('勤務時間');
        $sheet->getcell('L' . $nRow)->setValue('作業名');
        $sheet->getcell('M' . $nRow)->setValue('支給単価');
        $sheet->getcell('N' . $nRow)->setValue('支給金額');
        $sheet->getcell('O' . $nRow)->setValue('請求単価');
        $sheet->getcell('P' . $nRow)->setValue('請求金額');
        $sheet->getcell('Q' . $nRow)->setValue('備考');

        $nRow++;

        $saveClientId = null;
        $saveClientplaceId = null;
        $clientName = '';
        $clientPlaceName = '';
        foreach($Employeeworks as $Employeework)
        {
            // 顧客名と部門名を取得
            if($Employeework->client_id != $saveClientId)
            {
                $client = clients::find($Employeework->client_id);
                if($client)
                {
                    $clientName = $client->cl_name;
                }
                $saveClientId = $Employeework->client_id;
            }
            if($Employeework->clientplace_id != $saveClientplaceId)
            {
                $clientplace = clientplaces::find($Employeework->clientplace_id);
                if($clientplace)
                {
                    $clientPlaceName = $clientplace->cl_pl_name;
                }
                $saveClientplaceId = $Employeework->clientplace_id;
            }

            $sheet->getcell('A' . $nRow)->setValue($Employeework->employee->empl_cd . ' ' . $Employeework->employee->empl_name_last . ' ' . $Employeework->employee->empl_name_first);
            $sheet->getcell('B' . $nRow)->setValue($Employeework->wrk_date);
            $sheet->getcell('C' . $nRow)->setValue($Employeework->leave=='1' ? '有休' : ($Employeework->wrk_leave=='2' ? '特休' : ''));
            $sheet->getcell('D' . $nRow)->setValue($clientName);
            $sheet->getcell('E' . $nRow)->setValue($clientPlaceName);
            $sheet->getcell('F' . $nRow)->setValue($Employeework->wrk_log_start);
            $sheet->getcell('G' . $nRow)->setValue($Employeework->wrk_log_end);
            $sheet->getcell('H' . $nRow)->setValue($Employeework->wrk_work_start);
            $sheet->getcell('I' . $nRow)->setValue($Employeework->wrk_work_end);
            $sheet->getcell('J' . $nRow)->setValue($Employeework->wrk_leave);
            $sheet->getcell('K' . $nRow)->setValue($Employeework->wrk_work_hours);
            $sheet->getcell('L' . $nRow)->setValue($Employeework->summary_name);
            $sheet->getcell('M' . $nRow)->setValue($Employeework->payhour);
            $sheet->getcell('N' . $nRow)->setValue($Employeework->wrk_pay);
            $sheet->getcell('O' . $nRow)->setValue($Employeework->billhour);
            $sheet->getcell('P' . $nRow)->setValue($Employeework->wrk_bill);
            $sheet->getcell('Q' . $nRow)->setValue($Employeework->notes);
            $nRow++;
        }
        // create writer object
        $writer = new XlsxWriter($spreadsheet);
        // save file
        $writer->save(storage_path('data/kintai_detail.xlsx'));
        $fileName = '勤怠明細' . date('Y-m-d H:i') . '.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
         ];
        return response()->download(storage_path('data/kintai_detail.xlsx'), $fileName, $headers);
    }
}