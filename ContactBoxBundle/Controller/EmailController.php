<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Email;
use ContactBoxBundle\Form\EmailType;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends Controller
{
    /**
     * @Route("/{id}/addEmail")
     * @Method({"POST", "GET"})
     */
    public function addEmailAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('ContactBoxBundle:Contact')->find($id);

        $email = new Email();
        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $email->setContact($contact);
            $em->persist($email);
            $em->flush();

            $contactId = $email->getContact()->getId();

            $this->addFlash('notice','Email  was add');
            return $this->redirectToRoute('contactbox_contact_modify', ['id' => $contactId]);
        }

        return $this->render('@ContactBox/email/addEmail.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/modifyEmail")
     */
    public function modifyEmailAction(Request $request, $id)
    {
        $email = $this->getDoctrine()->getRepository('ContactBoxBundle:Email')->find($id);

        if (!$email) {
            throw $this->createNotFoundException('Email not found');
        }

        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $contactId = $email->getContact()->getId();

            $this->addFlash('notice','Email  was edit');
            return $this->redirectToRoute('contactbox_contact_show', ['id' => $contactId]);
        }

        return $this->render('@ContactBox/email/modify.htm.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("/{id}/deleteEmail")
     */
    public function deleteEmailAction($id)
    {
        $email = $this->getDoctrine()->getRepository('ContactBoxBundle:Email')->find($id);
        $contactId = $email->getContact()->getId();

        if (!$email) {
            throw $this->createNotFoundException('Email not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($email);
        $em->flush();

        $this->addFlash('notice','Email  was delete');
        return $this->redirectToRoute('contactbox_contact_show', ['id' => $contactId]);
    }
}
