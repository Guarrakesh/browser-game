<?php

namespace App\Helper;

use Doctrine\DBAL\Exception\RetryableException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class DBUtils
{
    /**
     * @throws RetryableException
     * @throws Exception
     */
    public static function transactionalRetry(ManagerRegistry $managerRegistry, string $managerName, callable $callback, int $maxRetries = 10): void
    {
        $entityManager = $managerRegistry->getManager($managerName);
        $retries = 0;
        $previousException = null;
        do {
            $entityManager->beginTransaction();

            try {
                $callback();

                $entityManager->flush();
                $entityManager->commit();

                return;
            } catch (RetryableException $e) {
                $entityManager->rollback();
                $entityManager->close();
                $managerRegistry->resetManager($managerName);
                $previousException = $e;
            } catch (Exception $e) {
                $entityManager->rollback();
                throw $e;
            }
        } while ($retries < $maxRetries);

        if ($previousException) {
            throw $previousException;
        }
    }
}