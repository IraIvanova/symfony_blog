<?php

namespace BlogBundle\Controller;

use AdminBundle\Entity\Blog;
use BlogBundle\Entity\BlogUser;
use BlogBundle\Entity\Comment;
use BlogBundle\Form\BlogUserType;
use BlogBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class BlogUserController extends Controller
{
    /**
     * @Template()
     */
    public function registrationAction(Request $request)
    {
        $blogUser = new BlogUser();
        $form =$this->createForm(BlogUserType::class, $blogUser);

        $form->handleRequest($request);
        if($request->isMethod("POST"))
        {
            $passwordHashed = $this->get('security.password_encoder')->encodePassword($blogUser, $blogUser->getPlainPassword());
            $blogUser->setPassword($passwordHashed);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($blogUser);
            $manager->flush();

            $this->addFlash('success', 'Спасибо за регистрацию!');
            return $this->redirectToRoute('blog_homepage');
        }

        return ['form'=>$form->createView()];
    }

    /**
     * @Template()
     */
    public function loginAction()
    {

        return [];
    }


}
