<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 18:13
 */

namespace demo\test\tcc;


class Constant
{
    /**
     * 创建的动作
     */
    const ACTION_CREATE = 'create';
    /**
     * 更新的动作
     */
    const ACTION_UPDATE = 'update';
    /**
     * 删除的动作
     */
    const ACTION_DELETE = 'delete';

    /**
     * tcc 的 try
     */
    const TCC_STEP_TRY = 'try';
    /**
     * tcc 的 cancel
     */
    const TCC_STEP_CANCEL = 'cancel';
    /**
     * tcc 的 confirm
     */
    const TCC_STEP_CONFIRM = 'confirm';
}