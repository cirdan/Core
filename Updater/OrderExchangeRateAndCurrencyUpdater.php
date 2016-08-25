<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderExchangeRateAndCurrencyUpdater implements OrderUpdaterInterface
{
    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var EntityManagerInterface
     */
    private $orderManager;

    /**
     * @param CurrencyContextInterface $currencyContext
     * @param RepositoryInterface $currencyRepository
     * @param EntityManagerInterface $orderManager
     */
    public function __construct(
        CurrencyContextInterface $currencyContext,
        RepositoryInterface $currencyRepository,
        EntityManagerInterface $orderManager
    ) {
        $this->currencyContext = $currencyContext;
        $this->currencyRepository = $currencyRepository;
        $this->orderManager = $orderManager;
    }

    /**
     * {@inheritdoc}
     */
    public function update(OrderInterface $order)
    {
        /** @var CurrencyInterface $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $this->currencyContext->getCurrencyCode()]);

        $order->setCurrencyCode($currency->getCode());
        $order->setExchangeRate($currency->getExchangeRate());

        $this->orderManager->persist($order);
        $this->orderManager->flush();
    }
}
