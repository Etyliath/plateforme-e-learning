<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\MyLessonRepository;
use App\Repository\ProgrammeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClasseController extends AbstractController
{
    #[Route('/classe', name: 'classe.index')]        
    /**
     * display all classes available 
     *
     * @return void
     */
    public function index(
        ProgrammeRepository $programmeRepository,
        MyLessonRepository $myLessonRepository): Response
    {

        $programmes = $programmeRepository->findAll();
        $myLesons = $myLessonRepository->findByUser($this->getUser());
        return $this->render('classe/classe.html.twig',[
            'programmes' => $programmes,
            'myLessons' => $myLesons
        ]);
    }

    #[Route('/classe/lesson', name: 'classe.lesson')]
    /**
     * display lesson buy by a user
     *
     * @param  mixed $myLessonRepository
     * @return Response
     */
    public function lesson(MyLessonRepository $myLessonRepository): Response
    {
        $myLessons = $myLessonRepository->findByUser($this->getUser());
        $programmes=[];
        foreach($myLessons as $myLesson){
            $programmes[] = $myLesson->getLesson()->getProgramme();
        }
        if(!empty($programmes)){
            $programmes = array_unique($programmes);
            return $this->render('classe/lesson.html.twig', [
                'programmes' => $programmes,
                'myLessons' => $myLessons,
            ]);
        }else{
            return $this->render('classe/lesson.html.twig', [
                'programmes' => $programmes,
                'myLessons' => $myLessons,
            ]);
        }
    }

    #[Route('/classe/content/{id}', name:'classe.content')]
    /**
     * display content of a lesson
     *
     * @param  mixed $lesson
     * @param  mixed $myLessonRepository
     * @return void
     */
    public function content(Lesson $lesson, MyLessonRepository $myLessonRepository)
    {
        $myLesson = $myLessonRepository->findOneBy([
            'user'=>$this->getUser(),
            'lesson' => $lesson
        ]);
        //dd($myLesson);
        return $this->render('classe/content.html.twig',[
            "lesson" => $lesson,
            "myLesson" => $myLesson
        ]);
    }

    #[Route('/classe/content/validate/{id}', name:'classe.validate')]
    /**
     * validate a lesson for a user connected
     *
     * @param  mixed $lesson
     * @param  mixed $myLessonRepository
     * @return void
     */
    public function validate(Lesson $lesson, MyLessonRepository $myLessonRepository,
    EntityManagerInterface $em)
    {

        $myLesson = $myLessonRepository->findOneBy([
            'user'=>$this->getUser(),
            'lesson' => $lesson
        ]);
        $myLesson->setValidated(true);
        $myLesson->setUpdatedAt(new DateTimeImmutable());
        $myLesson->setUpdatedBy($this->getUser()->getEmail());
        $em->flush();
        return $this->redirectToRoute('classe.lesson');
    }
    
}
