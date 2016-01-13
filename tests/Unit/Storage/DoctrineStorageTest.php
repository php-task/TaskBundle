<?php

namespace Unit\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Argument;
use Task\TaskBundle\Entity\Task as TaskEntity;
use Task\TaskBundle\Entity\TaskRepository;
use Task\TaskBundle\Storage\DoctrineStorage;
use Task\TaskInterface;

class DoctrineStorageTest extends \PHPUnit_Framework_TestCase
{
    public function storeDataProvider()
    {
        return [
            [new \DateTime(), true, '123-123-123'],
            [new \DateTime(), true, '321-321-321'],
            [new \DateTime('1 day ago'), true, '321-321-321'],
            [new \DateTime(), false, '123-123-123'],
        ];
    }

    /**
     * @dataProvider storeDataProvider
     */
    public function testStore($date, $completed, $uuid, $key = null)
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $repository = $this->prophesize(TaskRepository::class);

        $storage = new DoctrineStorage($entityManager->reveal(), $repository->reveal());

        $task = $this->prophesize(TaskInterface::class);
        $task->getUuid()->willReturn($uuid);
        $task->getKey()->willReturn($key);
        $task->isCompleted()->willReturn($completed);
        $task->getExecutionDate()->willReturn($date);

        $storage->store($task->reveal());

        $entityManager->persist(
            Argument::that(
                function (TaskEntity $entity) use ($date, $completed, $uuid) {
                    $this->assertEquals($uuid, $entity->getUuid());
                    $this->assertEquals($completed, $entity->isCompleted());
                    $this->assertEquals($date, $entity->getExecutionDate());

                    return true;
                }
            )
        )->shouldBeCalledTimes(1);
        $entityManager->flush()->shouldBeCalledTimes(1);
    }
}
