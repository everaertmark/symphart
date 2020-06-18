<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Psr\Log\LoggerInterface;


use App\Mailer\Emailer;
use App\Form\ContactType;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer, Emailer $emailer)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        $this->addFlash('success', 'good job');

        if ($form->isSubmitted() && $form->isValid() ) {

            $message = (new \Swift_Message('Hello Email'))
                        ->setFrom('send@example.com')
                        ->setTo('recipient@example.com')
                        ->setBody(
                            $this->renderView(
                                // templates/emails/registration.html.twig
                                'emails/registration.html.twig',
                                ['name' => $name]
                            ),
                            'text/html'
                        );

            

        }

        return $this->render('contact/contact.html.twig', [
            'our_form' => $form->createView()
        ]);
    }
}
