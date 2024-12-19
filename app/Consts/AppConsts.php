<?php

namespace App\Consts;

/**
 * AppConsts class
 * 
 * 定数クラス
 * 
 * @category Consts
 * @package  App\Consts
 */
class AppConsts
{
    /**
     * session variable key
     */
    const SESS_WORK_YEAR = 'work_year';
    const SESS_WORK_MONTH = 'work_month';
    const SESS_CLIENT_ID = 'client_id';
    const SESS_CLIENT_PLACE_ID = 'client_place_id';
    const SESS_SEARCH = 'search';
    const SESS_RETIRE = false;
    const SESS_PREVIOUS_URL = 'previous_url';

    /**
     * standard pagination
     */
    const PAGINATION = 15;
}