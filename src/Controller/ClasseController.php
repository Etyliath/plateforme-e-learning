<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\MyLesson;
use App\Repository\ThemeRepository;
use App\Repository\LessonRepository;
use App\Repository\MyLessonRepository;
use App\Repository\ProgrammeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClasseController extends AbstractController
{
    #[Route('/classe', name: 'classe.index')]
    public function index(ThemeRepository $themeRepository,
    ProgrammeRepository $cursusRepository,
    LessonRepository $lessonRepository,
    MyLessonRepository $myLessonRepository): Response
    {
        $themes = $themeRepository->findAll();
        $programmes = $cursusRepository->findAll();
        $lessons = $lessonRepository->findAll();
        $myLessons = $myLessonRepository->findByUser($this->getUser());
        return $this->render('classe/index.html.twig', [
            'themes' => $themes,
            'programmes' => $programmes,
            'lessons' => $lessons,
            'myLessons' => $myLessons
        ]);
    }

    #[Route('/classe/purchase-lesson/{id}', name: 'classe.purchase.lesson')]
    public function addLesson(int $id,
        LessonRepository $lessonRepository,
        MyLessonRepository $myLessonRepository,
        EntityManagerInterface $em): Response
    {
        //faire test
        $lesson = $lessonRepository->find($id);

        $myLesson = $myLessonRepository->findBy([
            'user'=>$this->getUser(),
            'lesson' => $lesson
        ]);
        if(!$myLesson){
            $this->addMyLesson($em, $lesson);
            $this->addFlash('success',$lesson->getName() . ' à bien été ajouté');
            return $this->redirectToRoute('classe.index');
        }else{
            $this->addFlash('warning','la leçon' .$lesson->getName() . ' est déjà en votre possesion');
            return $this->redirectToRoute('classe.index');
        }
        
    }

    #[Route('/classe/purchase-programme/{id}', name: 'classe.purchase.programme')]
    public function addProgramme(int $id,
        LessonRepository $lessonRepository,
        ProgrammeRepository $programmeRepository,
        MyLessonRepository $myLessonRepository,
        EntityManagerInterface $em): Response
    {
        $programme = $programmeRepository->find($id);
        $lessons = $lessonRepository->findByProgrammeId($programme->getId());

        foreach($lessons as $lesson){
            $myLesson = $myLessonRepository->findBy([
                'user'=>$this->getUser(),
                'lesson' => $lesson
            ]);
            if(!$myLesson){
                $tmpLessons []= $lesson;
            }else{
                $this->addFlash('warning','la leçon' . $lesson->getName() . ' est déjà en votre possesion');
                return $this->redirectToRoute('classe.index');
            } 
        }
        foreach($tmpLessons as $tmpLesson){
            $this->addMyLesson($em, $tmpLesson);
            $this->addFlash('success',$tmpLesson->getName() . ' à bien été ajouté');

        }
        return $this->redirectToRoute('classe.index');
    }

    #[Route('/classe/lesson', name: 'classe.lesson')]
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

    
    /**
     * validate
     *
     * @param  mixed $lesson
     * @param  mixed $myLessonRepository
     * @return void
     */
    #[Route('/classe/content/validate/{id}', name:'classe.validate')]
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
    
    /**
     * addMyLesson
     *
     * @param  mixed $em
     * @param  mixed $lesson
     * @return void
     */
    private function addMyLesson(EntityManagerInterface $em, Lesson $lesson )
    {
        $myLessonAdd = new MyLesson();
        $myLessonAdd->setUser($this->getUser());
        $myLessonAdd->setLesson($lesson);
        $myLessonAdd->setPurchased(true);
        $myLessonAdd->setValidated(false);
        $myLessonAdd->setCreatedAt(new DateTimeImmutable());
        $myLessonAdd->setUpdatedAt(new DateTimeImmutable());
        $myLessonAdd->setCreatedBy($this->getUser()->getEmail());
        $myLessonAdd->setUpdatedBy($this->getUser()->getEmail());
        $em->persist($myLessonAdd);
        $em->flush();

    }
}
