<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Contact;
use ContactBoxBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    /**
     * @Route("/new")
     * @Method({"POST", "GET"})
     */
    public function newAction(Request $request)
    {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($contact);
            $em->flush();

            $this->addFlash('notice','Contact  was add');
            return $this->redirectToRoute('contactbox_contact_showall');
        }

        return $this->render('@ContactBox/contact/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/")
     */
    public function showAllAction()
    {
        $contacts = $this
            ->getDoctrine()
            ->getRepository('ContactBoxBundle:Contact')
            ->findBy([], ['surname' =>'ASC']);

        return $this->render('@ContactBox/contact/showAll.html.twig',['contacts' => $contacts]);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction($id)
    {
        $contact = $this
            ->getDoctrine()
            ->getRepository('ContactBoxBundle:Contact')
            ->find($id);

        return $this->render('@ContactBox/contact/show.html.twig',
            ['contact' => $contact]);
    }

    /**
     * @Route("/{id}/modify")
     */
    public function modifyAction(Request $request, $id)
    {
        $contact = $this
            ->getDoctrine()
            ->getRepository('ContactBoxBundle:Contact')
            ->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->addFlash('notice','Contact  was edit');
            return $this->redirectToRoute('contactbox_contact_showall');
        }

        return $this->render('@ContactBox/contact/modify.html.twig', ['form' => $form->createView(), 'contact' => $contact]);
    }

    /**
     * @Route("/{id}/delete")
     */
    public function deleteAction($id)
    {
        $contact = $this
            ->getDoctrine()
            ->getRepository('ContactBoxBundle:Contact')
            ->find($id);

        if (!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($contact);
        $em->flush();

        $this->addFlash('notice','Contact  was delete');
        return $this->redirectToRoute('contactbox_contact_showall');
    }
}
