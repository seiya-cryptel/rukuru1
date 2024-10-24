<?php

namespace App\Services;

class WorkhoursType1 extends WorkhoursBase
{

    /**
     * WorkhoursBase constructor
     * @param protected readonly int $client_id
     * @param protected readonly int $clientplace_id
     * @param protected readonly int $targetYear
     * @param protected readonly int $targetMonth
     * @param protected readonly int $employee_id
     */
    public function __construct(
        int $client_id,
        int $clientplace_id,
        int $targetYear,
        int $targetMonth,
        int $employee_id
    )
    {
        parent::__construct($client_id, $clientplace_id, $targetYear, $targetMonth, $employee_id);
    }
    
    protected function createWorkDay(int $day) : WorkDayBase
    {
        return new WorkDayType1($this, $day);
    }
}