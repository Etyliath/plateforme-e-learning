<?php
namespace App\EventSubscriber;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Programme;
use App\Entity\Theme;
use DateTimeImmutable;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use phpDocumentor\Reflection\Types\This;

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
     * setDateTimeAndByUserCreate
     *put and update datetime for create_at up au on entity declared in dashboard EasyAdmin
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