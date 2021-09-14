<?php

namespace Mashbo\CoreTesting\Support\Application;

use Money\Money;

trait MoneyTestTrait
{
    public function formatMoney(Money $money): string
    {
        return '£' . number_format($money->getAmount() / 100, 2, '.', ',');
    }
}
