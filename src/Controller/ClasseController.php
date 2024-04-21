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
    LessonRepository $lessonRepository): Response
    {
        $themes = $themeRepository->findAll();
        $programmes = $cursusRepository->findAll();
        $lessons = $lessonRepository->findAll();
        return $this->render('classe/index.html.twig', [
            'themes' => $themes,
            'programmes' => $programmes,
            'lessons' => $lessons
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
            $this->addFlash('success','La leçon à bien été ajouté');
            return $this->redirectToRoute('classe.index');
        }else{
            $this->addFlash('warning','vous pocèder déjà cette leçon');
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
                $this->addMyLesson($em, $lesson);
                $this->addFlash('success','La leçon à bien été ajouté');
                
            }else{
                $this->addFlash('warning','vous pocèder déjà cette leçon');
            } 

        }
        return $this->redirectToRoute('classe.index');
    }

    #[Route('/classe/lesson', name: 'classe.lesson')]
    public function lesson(MyLessonRepository $myLessonRepository,ProgrammeRepository $programmeRepository): Response
    {

        $myLessons = $myLessonRepository->findAll();
        return $this->render('classe/lesson.html.twig', [
            'myLessons' => $myLessons,
        ]);
    }

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
