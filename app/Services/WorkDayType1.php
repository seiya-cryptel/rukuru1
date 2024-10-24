<?php

namespace App\Services;

class WorkDayType1 extends WorkDayBase
{
    /**
     * WorkDayBase constructor
     * @param protected WorkHoursBase $workHours
     * @param protected readonly int $day
     */
    public function __construct(
        WorkHoursBase $workHours,
        int $day)
        {
            parent::__construct($workHours, $day);
        }
}