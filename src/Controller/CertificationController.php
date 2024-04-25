<?php

namespace App\Controller;

use App\Repository\LessonRepository;
use App\Repository\MyLessonRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CertificationController extends AbstractController
{
    #[Route('/certification', name: 'certification.index')]
    public function index(LessonRepository $lessonRepository,
    ThemeRepository $themeRepository,
    MyLessonRepository $myLessonRepository): Response
    {
        $themesCertficated = [];
        $themes = $themeRepository->findAll();
        /**
         * loop for get themes have a certification validate for a user
         */
        foreach($themes as $theme){
            $programmes = $theme->getProgrammes();
            $programmeValidate = [];
            foreach($programmes as $programme){
                $lessons = $programme->getLessons();
                $lessonValidate = [];
                foreach($lessons as $lesson){
                    $myLessonFind = $myLessonRepository->findOneBy([
                        'user'=>$this->getUser(),
                        'lesson' => $lesson
                    ]);
                    $lessonIsValidate = false;
                    if($myLessonFind!== null ){
                        if($myLessonFind->isValidated()){
                            $lessonIsValidate = true;
                        }
                    }else{
                        $lessonIsValidate = false;
                    }
                    $lessonValidate[] = $lessonIsValidate;
                    
                }
                $allLessonValidate = array_reduce($lessonValidate,function($carry, $item) {
                    return $carry && $item;
                }, true);
                $programmeValidate [] = $allLessonValidate;
                
            }
            $allProgrammeValidate = array_reduce($programmeValidate,function($carry, $item) {
                return $carry && $item;
            }, true);
            $themesCertficated []= [
                'theme' => $theme->getName(),
                'certficate' => $allProgrammeValidate];
        }
        return $this->render('certification/index.html.twig', [
            'themesCertificate' => $themesCertficated
        ]);
    }
}
