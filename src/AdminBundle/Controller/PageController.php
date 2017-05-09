<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\EnquiryType;
use AdminBundle\Entity\Enquiry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Page:index.html.twig');
    }

    public function pageAboutAction()
    {
        return $this->render('AdminBundle:Page:about.html.twig');
    }

    /**
     * @Template()
     */
    public function contactAction(Request $request)
    {
        $enquiry = new Enquiry();

        $form = $this->createForm(EnquiryType::class, $enquiry);

        if($request->isMethod("Post")) {

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $validator = $this->get('validator');
                $errors = $validator->validate($enquiry);

                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error->getMessage());
                    }
                }
                    $email = $enquiry->getEmail();

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($enquiry);
                    $manager->flush();

                    $notification = $this->get("myblog.mail_sender");
                    $message = "New message!";

                    $notification->sendMail("avonavis@gmail.com", $email, $message);// сделать шаблон для письма

                    $this->addFlash('success', $message);

                    return $this->redirectToRoute('blog_homepage');

            }
        }

        return ['form'=>$form->createView(),
        'enquiry'=>$enquiry
        ] ;
    }
}