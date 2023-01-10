<?php

namespace App\Controller;

use App\Entity\Noticia;
use App\Entity\User;
use App\Repository\NoticiaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class NoticiaController extends AbstractController
{
    #[Route('/noticias', name: 'app_noticia' , methods:"GET")]
    public function index(NoticiaRepository $noticiaRapository):Response
    {
      $noticia = $noticiaRapository->findAll();
      $reponse = $this->json($noticia,200,[],['groups' => 'Noticias:read']); 
      return $reponse;
    }

    #[Route('/api/newnoticia', name: 'app_noticias', methods: "POST")]
    public function newNoticias(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ManagerRegistry $doctrine, ValidatorInterface $val)
    {
        $jsonRecu = $request->getContent();
    
        

        try {
            $data = json_decode($jsonRecu);

            $id = $data->autor;
            
            $id = $doctrine->getRepository(User::class)->find($id);
         
            $noticias = $serializer->deserialize($jsonRecu, Noticia::class, 'json');
            $noticias->setautor($id);
       
            $err = $val->validate($noticias);
   
            if (count($err) > 0) {
                return $this->json($err, 400);
            }

            $em->persist($noticias);
            $em->flush();

            return $this->json($noticias, 201, [], ['groups' => 'Noticias:read']);
        } catch (NotEncodableValueException  $e) {
            return $this->json([
                'status' => 400,
                'message' =>  $e->getMessage(),
            ], 400);
        }
    }
}
