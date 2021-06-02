<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Entity\Notes;
use App\Repository\NotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class NoteController extends AbstractController
{
    /**
     * @Route ("/api/note/{id}", name="note", methods={"POST"})
     * @OA\Post (
     *     tags={"Note"},
     *     summary="Ajouter une note",
     *      @OA\Parameter (
     *              name="id",
     *              in="path",
     *              description="L'id est celui de l'élève correspondant",
     *              @OA\Schema (type="integer"),
     *          ),
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="integer", property="id"),
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur", minimum="0", maximum="20")
     *          )
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="La note a été ajouter",
     *          @OA\JsonContent(
     *              @OA\Property (type="integer", property="id"),
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur", minimum="0", maximum="20")
     *          ),
     *      ),
     *     @OA\Response(
     *          response="400",
     *          description="Ajout de la note impossible les champs sont invalides"
     *      )
     * )
     *
     */
    public function addNote(int $id , Request $request, EntityManagerInterface $em){

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $eleve = $em->getRepository(Eleves::class)->find($id);


            if ($data['valeur'] < 0 || $data['valeur'] > 20)
            {
                return $this->json('La note doit être comprise entre 0 et 20', 400);
            } elseif ($data['matiere'] == ''){
                return $this->json('Le champ matiere ne doit pas être vide', 400);
            }

            $note = new Notes();
            $note->setMatiere($data['matiere'])
                 ->setValeur($data['valeur'])
                 ->setEleves($eleve);

            $em->persist($note);
            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);

    }

    /**
     * @Route("/api/note/{id}", name="editNote", methods={"PUT"})
     * @OA\Put(
     *     tags={"Note"},
     *     summary="Modifier une note",
     *     @OA\Parameter(
     *         name="id",
     *         in = "path",
     *          description="L'id de la note correspondant",
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"valeur, matiere"},
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur", minimum="0", maximum="20")
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="La note a été modifié avec succès",
     *          @OA\JsonContent(
     *              @OA\Property (type="integer", property="id"),
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur", minimum="0", maximum="20")
     *          ),
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Modification de la note impossible les champs sont invalides"
     *      )
     *
     * )
     */

    public function editNote(int $id, Request $request, EntityManagerInterface $em){

         $data = json_decode($request->getContent(), true);
         $request->request->replace(is_array($data) ? $data : array());

         $note = $em->getRepository(Notes::class)->find($id);

         $note->setValeur($data['valeur'])
              ->setMatiere($data['matiere']);

         $em->flush();

         return $this->json($note, 201, [], ['groups' => 'eleve']);

    }

    /**
     * @Route("/api/note", name="deleteNote", methods={"DELETE"})
     * @OA\Delete(
     *     tags={"Note"},
     *     summary="Supprimer une note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="La note a été supprimé avec succès"
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Id inexistant"
     *      )
     * )
     */

    public function supprimerNote(int $id, EntityManagerInterface $em){

        $note = $em->getRepository(Notes::class)->find($id);

        if ($note == null){
            return $this->json("Aucune notes ne possède l'id (" .$id.")" , 400);
        }

        $em->remove($note);
        $em->flush();

        return $this->json("La note possèdant l' ".$id." a été supprimé",
            200, [], []);

    }

    /**
     * @Route("/api/note", name="average_all_note", methods={"GET"})
     * @OA\Get(
     *     tags={"Note"},
     *     summary="Moyenne de la classe",
     *     @OA\Response(
     *          response="200",
     *          description="La moyenne a été calculer avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(type="integer", property="moyenne de la classe"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Aucune note a été trouver"
     *      )
     *)
     */

    public function averageAllNote(NotesRepository $repository)
    {
        $notes = $repository->findAll();
        $sum = 0;

        foreach ($notes as $note){
            $sum += $note->getValeur();
        }

        $averageNote=  round(($sum / count($notes)), 2);
        return $this->json(['average' => $averageNote], 201, [], ['groups' => 'eleve']);
    }

}
