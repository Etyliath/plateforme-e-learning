<?php
namespace App\EventSubscriber;

use App\Entity\Lesson;
use App\Entity\Programme;
use App\Entity\Theme;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setDateTimeAndByUserToCreateEntity'],
            BeforeEntityUpdatedEvent::class => ['setDateTimeAndByUserToUpdateEntity'],
        ];
    }

    /**
     * setDateTimeAndByUserToCreateEntity
     * put datetime on field created_at and updated_at
     * put user on field created_by and update_at
     * when a entity(theme, progarmme, lesson) is persist in dashboard EasyAdmin
     * @param  mixed $event
     * @return void
     */
    public function setDateTimeAndByUserToCreateEntity(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Theme) {
            $this->createUpdateProperiesEntity($entity);
        }elseif ($entity instanceof Programme){
            $this->createUpdateProperiesEntity($entity);
        }elseif ($entity instanceof Lesson){
            $this->createUpdateProperiesEntity($entity);
        }else{
            return;
        }

    }

        
    /**
     * setDateTimeAndByUserToUpdateEntity
     * update datetime on field updated_at
     * update user on field update_at
     * when a entity(theme, progarmme, lesson) is updated in dashboard EasyAdmin
     * @param  mixed $event
     * @return void
     */
    public function setDateTimeAndByUserToUpdateEntity(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Theme) {
            $this->createUpdateProperiesEntity($entity);
        }elseif ($entity instanceof Programme){
            $this->createUpdateProperiesEntity($entity);
        }elseif ($entity instanceof Lesson){
            $this->createUpdateProperiesEntity($entity);
        }else{
            return;
        }

    }
    
    /**
     * createUpdateProperiesEntity
     * function call to put data on fields
     * updated_at, updated_by, created_at, createed_by on enttiy
     * @param  mixed $entity
     * @return void
     */
    private function createUpdateProperiesEntity($entity)
    {
        $userMail = $this->security->getUser()->getEmail();
        $entity->setUpdatedAt(new DateTimeImmutable());
        $entity->setUpdatedBy($userMail);
        if(!$entity->getId()){
            $entity->setCreatedAt(new DateTimeImmutable());
            $entity->setCreatedBy($userMail);
        }
    }


}