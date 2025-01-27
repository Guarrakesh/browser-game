<?php

namespace App\Helper;

use Doctrine\DBAL\Exception\RetryableException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;

trait TransactionTrait
{
    /**
     * @throws RetryableException
     */
    private function transactionalRetry(ManagerRegistry $managerRegistry, string $managerName, callable $callback, int $maxRetries = 10): mixed
    {
        $entityManager = $managerRegistry->getManager($managerName);
        $retries = 0;
        $previousException = null;
        do {
            $entityManager->beginTransaction();

            try {
                $result = $callback();

                $entityManager->flush();
                $entityManager->commit();

                return $result;
            } catch (RetryableException $e) {
                if (property_exists($this, 'logger') && $this->logger instanceof LoggerInterface) {
                    $this->logger->warning(sprintf("[Manager %s] Transaction locking failed. Retrying. [%s/%s retries]", $managerName, $retries, $maxRetries));
                }
                $entityManager->rollback();
                $entityManager->close();
                $managerRegistry->resetManager($managerName);
                $previousException = $e;
            } catch (Exception $e) {

                $entityManager->rollback();
                throw $e;
            }
        } while ($retries < $maxRetries);


        throw $previousException;

    }
}