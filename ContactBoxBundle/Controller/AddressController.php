<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Address;
use ContactBoxBundle\Form\AddressType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends Controller
{
    /**
     * @Route("{id}/addAddress")
     * @Method({"POST", "GET"})
     */
    public function addAddressAction(Request $request, $id)
    {
        $contact = $this->getDoctrine()->getRepository('ContactBoxBundle:Contact')->find($id);

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $address->setContact($contact);
            $em->persist($address);
            $em->flush();

            $contactId = $contact->getId();

            $this->addFlash('notice','Address  was add');
            return $this->redirectToRoute('contactbox_contact_modify', ['id' => $contactId]);
        }

        return $this->render('@ContactBox/address/addAddress.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("/{id}/modifyAddress")
     */
    public function modifyAddressAction(Request $request, $id)
    {
        $address = $this->getDoctrine()->getRepository('ContactBoxBundle:Address')->find($id);

        if (!$address) {
            throw $this->createNotFoundException('Address not found');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $contactId = $address->getContact()->getId();

            $this->addFlash('notice','Address  was edit');
            return $this->redirectToRoute('contactbox_contact_show', ['id' => $contactId]);
        }

        return $this->render('@ContactBox/address/modify.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/deleteAddress")
     */
    public function deleteAddressAction($id)
    {
        $address = $this->getDoctrine()->getRepository('ContactBoxBundle:Address')->findOneById($id);
        $contactId = $address->getContact()->getId();

        if (!$address) {
            throw $this->createNotFoundException('Address not found');
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($address);
        $em->flush();

        $this->addFlash('notice','Address  was deleted');

        return $this->redirectToRoute('contactbox_contact_show', ['id' => $contactId]);
    }
}