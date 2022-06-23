<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private static $nestingLevel = 0;
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private DataPersisterInterface $decoratedDataPersister;
    private LoggerInterface $logger;
    private Security $security;

    // public function __construct(DataPersisterInterface $decoratedDataPersister,  EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    public function __construct(
        DataPersisterInterface $decoratedDataPersister,
        UserPasswordEncoderInterface $userPasswordEncoder,
        LoggerInterface $loggerInterface,
        Security $security
    )
    // public function __construct(DataPersisterInterface $decoratedDataPersister, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        // dump(__METHOD__);
        // $this->entityManager = $entityManager;

        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->logger = $loggerInterface;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool
    {
        // dump(__METHOD__);
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context = [])
    {


        if (($context['item_operation_name'] ?? null) === 'put') {
            $this->logger->info(sprintf('User "%s" is being updated!', $data->getId()));
        }


        if (!$data->getId()) {
            // take any actions needed for a new user
            // send registration email
            // integrate into some CRM or payment system
            $this->logger->info(sprintf('User %s just registered! Eureka!', $data->getEmail()));
        }



        // dump('Nesting level: ' . ++self::$nestingLevel);
        // $this->print_mem();
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }

        // now handled in a listener
        //$data->setIsMe($this->security->getUser() === $data);

        $d = $this->decoratedDataPersister->persist($data);
    }

    public function remove($data, array $context = [])
    {
        // dump(__METHOD__);
        // $this->entityManager->remove($data);
        // $this->entityManager->flush();
        $this->decoratedDataPersister->remove($data);
    }


    function print_mem()
    {
        /* Currently used memory */
        $mem_usage = memory_get_usage();

        /* Peak memory usage */
        $mem_peak = memory_get_peak_usage();

        dump('The script is now using: <strong>' . round($mem_usage / 1024) . 'KB</strong> of memory.<br>');
        dump('Peak usage: <strong>' . round($mem_peak / 1024) . 'KB</strong> of memory.<br><br>');
    }
}
