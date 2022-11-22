<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
            $this->entityManager->remove($data);
            $this->entityManager->flush();
        }
    
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->passwordHasher->hashPassword($data, $data->getPlainPassword())
            );
            $data->eraseCredentials();
        }
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
        return $result;
    }
}