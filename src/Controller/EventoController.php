<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Evento;
use App\Repository\EventoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class EventoController extends AbstractController
{
    #[Route('/eventos', name: 'app_evento', methods: "GET")]
    public function index(EventoRepository $eventoRepository): Response
    {
        $evento = $eventoRepository->findAll();
        $response = $this->json($evento, 200, [], ['groups' => 'evento:read']);
        return $response;
    }

    //Post NewEvento
    #[Route('/api/newevento', name: 'app_newevento', methods: "POST")]
    public function newEvento(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ManagerRegistry $doctrine , ValidatorInterface $val)
    {

        $jsonRecu = $request->getContent();
        
        try {
            $data = json_decode($jsonRecu);

            $id = $data->autor;
            $id = $doctrine->getRepository(User::class)->find($id);

            $evento = $serializer->deserialize($jsonRecu, Evento::class, 'json');
            $evento->setautor($id);

            $err = $val->validate($evento);
            
            if(count($err)> 0){
                return $this->json($err,400);
            }

            $em->persist($evento);
            $em->flush();

            return $this->json($evento, 201, [], ['groups' => 'evento:read']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' =>  $e->getMessage(),
            ], 400);
        }
    }
}
