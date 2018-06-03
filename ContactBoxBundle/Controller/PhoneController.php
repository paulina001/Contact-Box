<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Phone;
use ContactBoxBundle\Form\PhoneType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PhoneController extends Controller
{
    /**
     * @Route("{id}/addPhone")
     * @Method({"POST", "GET"})
     */
    public function addPhoneAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('ContactBoxBundle:Contact')->find($id);

        $phone = new Phone();
        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $phone->setContact($contact);
            $em->persist($phone);
            $em->flush();

            $contactId = $phone->getContact()->getId();

            $this->addFlash('notice','Phone  was add');
            return $this->redirectToRoute('contactbox_contact_modify', ['id' => $contactId]);
        }

        return $this->render('@ContactBox/phone/addPhone.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("/{id}/modifyPhone")
     */
    public function modifyPhoneAction(Request $request, $id)
    {
        $phone = $this->getDoctrine()->getRepository('ContactBoxBundle:Phone')->find($id);

        if (!$phone) {
            throw $this->createNotFoundException('Phone not found');
        }

        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $contactId = $phone->getContact()->getId();

            $this->addFlash('notice','Phone  was edit');
            return $this->redirectToRoute('contactbox_contact_show', ['id' => $contactId]);
        }

        return $this->render('@ContactBox/phone/modify.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/deletePhone")
     */
    public function deletePhoneAction($id)
    {
        $phone = $this->getDoctrine()->getRepository('ContactBoxBundle:Phone')->find($id);
        $contactId = $phone->getContact()->getId();

        if (!$phone) {
            throw $this->createNotFoundException('Phone not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($phone);
        $em->flush();

        $this->addFlash('notice','Phone  was delete');
        return $this->redirectToRoute('contactbox_contact_show', ['id' => $contactId]);
    }
}
