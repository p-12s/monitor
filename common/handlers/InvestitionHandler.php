<?php

namespace common\handlers;

use common\helpers\DateUtil;
use common\models\Investition;
use common\models\CashTransaction;
use common\models\ProfitCalculator;
use common\models\User;
use Exception;
use DateTime;

class InvestitionHandler
{
    /**
     * Запуск обработки инвестиций
     * @return void
     * @throws Exception
     */
    public function runClosingProcess()
    {
        $deposits = Investition::getAllActive();

        // расчет средневзвешенной %-й ставки.
        // Он будет одинаковым для всех вкладов, закрывающихся сегодня
        $profitCalculator = new ProfitCalculator(DateUtil::findFirstDayOfCurrentMonth(), DateUtil::now());
        $interestForGrand = $profitCalculator->calculateInterestForClose(Investition::TYPE_GRAND);
        $interestForGrandElite = $profitCalculator->calculateInterestForClose(Investition::TYPE_GRAND_ELITE);

        foreach($deposits as $item)
        {
            if ( !DateUtil::isFullYearAlreadyPassed(new DateTime( $item['created_at'])) ) {
                continue;
            }
            echo 'id='. $item['id'] .' готов к закрытию, прошел год: ' . $item['created_at'];
            self::processInvestition( $item, $interestForGrand, $interestForGrandElite );
            echo '. Вклад успешно закрыт' . PHP_EOL;
        }
    }

    /**
     * Процесс обработки закрытия инвестиции (расчет прибыли, закрытие, пополн. баланса, сохр. транзакций)
     * @param $deposit
     * @param $interestForGrand
     * @param $interestForGrandElite
     * @return void
     */
    protected static function processInvestition( $investition, $interestForGrand, $interestForGrandElite )
    {
        // подготовим полученные данные
        $investmentId = (int)$investition['id'];
        $investmentTypeId = (int)$investition['type_id'];
        $userId = (int)$investition['user_id'];
        $sum = (float)$investition['sum'];
        $profit = (float)$investition['profit'];

        $profitForDb = 0;
        if ( $investmentTypeId === (int)Investition::TYPE_GRAND ) {
            $lastProfit = $sum * $interestForGrand; // Grand - начисляем % на основную сумму
        } else {
            $lastProfit = ($sum + $profit) * $interestForGrandElite; // Elite - начисляем % на основную сумму и полученную ранее прибыль
            $profitForDb = $lastProfit;
        }

        // Закрыть инвестицию
        self::close( $userId, $investmentId, $profitForDb, $investmentTypeId, $sum );

        // Перевести основную сумму и прибыль на баланс
        $totalSum = $sum + $lastProfit;
        User::rechargeBalance( $userId, $totalSum );
    }

    /**
     * Закрытие инвестиции (НЕ ЗАБЫВАЕМ УВЕЛИЧИТЬ БАЛАНС НА ВЕЛИЧИНУ $totalAmount, ЗДЕСЬ ЭТО НЕ ДЕЛАЮ !!!)
     * @param $userId
     * @param $investmentId
     * @param $profit
     * @param $type
     * @param $sum
     * @return void
     */
    protected static function close( $userId, $investmentId, $profit, $type, $sum )
    {
        $investment  = Investition::find()
            ->where(['id' => $investmentId])
            ->andWhere(['user_id' => $userId])
            ->andWhere(['status' => Investition::STATUS_ACTIVE])
            ->one();
        if ( !$investment ) {
            trigger_error('Не удалось найти инвестицию для закрытия: ' .
                "investmentId - $investmentId, userId - $userId", E_ERROR);
        }

        // Для Grand Elite запишем прибыль за год в столбец Profit (вдруг пригодится в будущем)
        $profitToTableCell = ( $type === (int)Investition::TYPE_GRAND_ELITE ) ? $profit : 0;

        $investment->profit = $profitToTableCell;
        $investment->status = Investition::STATUS_CLOSED;
        $investment->closed_at = date('Y-m-d H:i:s');
        $investment->save();

        // В историю транзакций запишем общую сумму.
        $totalAmount = $sum + $profit;
        CashTransaction::saveIntoDb( $userId, $totalAmount, CashTransaction::TYPE_CLOSING_INVESTMENT );
    }

    /**
     * Подсчет суммы списка инвестиций
     * @param $list array
     * @return float
     */
    public static function calculateSum( $list )
    {
        $sum = 0;
        foreach ($list as $item) {
            if (!($item instanceof Investition)) {
                trigger_error('Передан неверный тип объекта - должен быть тип Investition', E_USER_ERROR);
            }
            $sum += (float)$item['sum'];
        }
        return $sum;
    }
}
