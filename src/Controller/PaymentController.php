<?php

namespace App\Controller;

use App\Entity\Lesson;
use DateTimeImmutable;
use App\Entity\MyLesson;
use App\Repository\LessonRepository;
use App\Repository\MyLessonRepository;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    
    #[Route('/payment/lesson/{id}', name: 'payment.lesson')]    
    /**
     * buy a Lesson
     *
     * @param  mixed $id
     * @return void
     */
    public function addLesson(int $id,
        LessonRepository $lessonRepository,
        MyLessonRepository $myLessonRepository,
        EntityManagerInterface $em): Response
    {
        $lesson = $lessonRepository->find($id);
        $myLesson = $myLessonRepository->findBy([
            'user'=>$this->getUser(),
            'lesson' => $lesson
        ]);
        if(!$myLesson){
            //start payement in Stripe
            $cart [] =[
                    'name'=> $lesson->getName(),
                    'price' => $lesson->getPrice(),
                ];
            $payment = new \App\Service\StripeCheckoutSession($_ENV['STRIPE_SECRET']);
            $stripeCheckout = $payment->createSession($cart);
            $checkoutId = $stripeCheckout->id;
            $checkoutUrl = strval($stripeCheckout->url);
            // dd($checkoutUrl);
            $this->addMyLesson($em, $lesson,$checkoutId);
            $this->addFlash('success',$lesson->getName() . ' à bien été ajouté');
            return new RedirectResponse($checkoutUrl);
        }else{
            $this->addFlash('warning','la leçon' .$lesson->getName() . ' est déjà en votre possesion');
            return $this->redirectToRoute('classe.index');
        }
    }

    #[Route('/payment/programme/{id}', name: 'payment.programme')]
    /**
     * buy a programme 
     *
     * @param  mixed $id
     * @return void
     */
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

        $cart [] = [
                'name'=> $programme->getName(),
                'price' => $programme->getPrice(),
            ];
        
        //start payement in Stripe
        $payment = new \App\Service\StripeCheckoutSession($_ENV['STRIPE_SECRET']);
        $stripeCheckout = $payment->createSession($cart);
        $checkoutId = $stripeCheckout->id;
        $checkoutUrl = strval($stripeCheckout->url);
        

        foreach($tmpLessons as $tmpLesson){
            $this->addMyLesson($em, $tmpLesson, $checkoutId);
            $this->addFlash('success',$tmpLesson->getName() . ' à bien été ajouté');
        }
        return new RedirectResponse($checkoutUrl);
    }


    #[Route('/payment/success/{sessionId}', name: 'payment.success')]    
    /**
     * success payment update myLesson to validate purchased value
     *
     * @param  mixed $sessionId
     * @param  mixed $myLessonRepository
     * @param  mixed $em
     * @return Response
     */
    public function success(string $sessionId, MyLessonRepository $myLessonRepository, EntityManagerInterface $em): Response
    {

        $myLessons = $myLessonRepository->findBySessionId($sessionId);
        foreach($myLessons as $myLesson){
            $myLesson->setPurchased(true);
            $em->flush();
        }
        return $this->render('payment/success.html.twig', [
            
        ]);
    }

    #[Route('/payment/cancel', name: 'payment.cancel')]    
    /**
     * cancel page when payment is aborded
     *
     * @return Response
     */
    public function cancel(): Response
    {
        
        return $this->render('payment/cancel.html.twig');
    }

        /**
     * add a Lesson and user on table MyLesson in database
     * and default value of all fields
     *
     * @param  mixed $em
     * @param  mixed $lesson
     * @return void
     */
    private function addMyLesson(EntityManagerInterface $em, Lesson $lesson, string $sessionId )
    {
        $myLessonAdd = new MyLesson();
        $myLessonAdd->setUser($this->getUser());
        $myLessonAdd->setLesson($lesson);
        $myLessonAdd->setPurchased(false);
        $myLessonAdd->setValidated(false);
        $myLessonAdd->setSessionId($sessionId);
        $myLessonAdd->setCreatedAt(new DateTimeImmutable());
        $myLessonAdd->setUpdatedAt(new DateTimeImmutable());
        $myLessonAdd->setCreatedBy($this->getUser()->getEmail());
        $myLessonAdd->setUpdatedBy($this->getUser()->getEmail());
        $em->persist($myLessonAdd);
        $em->flush();

    }

}
