<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Entity\Notes;
use App\Form\ElevesType;
use App\Form\NotesType;
use App\Repository\NotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class NoteController extends AbstractController
{
    /**
     * @Route ("/api/note", name="note", methods={"POST"})
     * @OA\Post (
     *     tags={"Note"},
     *     summary="Ajouter une note",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(type="integer", property="eleves"),
     *              @OA\Property(type="string", property="matiere"),
     *              @OA\Property(type="integer", property="valeur"),
     *          )
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="La note a été ajouter",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Notes")
     *          ),
     *      ),
     *     @OA\Response(
     *          response="400",
     *          description="Ajout de la note impossible les champs sont invalides"
     *      )
     * )
     *
     */
    public function addNote(Request $request)
    {

        $note = new Notes();

        $data=json_decode($request->getContent(), true);

        return $this->saveNote($note, $data);

    }

    /**
     * @Route("/api/note/{id}", name="editNote", methods={"PUT"})
     * @OA\Put(
     *     tags={"Note"},
     *     summary="Modifier une note",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(type="integer", property="eleves"),
     *              @OA\Property(type="string", property="matiere"),
     *              @OA\Property(type="integer", property="valeur"),
     *
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="La note a été modifié avec succès",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Notes")
     *          ),
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Modification de la note impossible les champs sont invalides"
     *      )
     *
     * )
     */

    public function editNote(int $id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository(Notes::class)->find($id);

        if (!$note) {
            throw new ResourceNotFoundException("Resource $id not found");
        }

        $data=json_decode($request->getContent(), true);

        return $this->saveNote($note, $data);

    }

    /**
     * @Route("/api/note/{id}", name="deleteNote", methods={"DELETE"})
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

    private function saveNote($note, $data)
    {

        $requestBody = $data;

        $form = $this->createForm(NotesType::class, $note);

        $form->submit($requestBody);

        if ($form->isSubmitted()) #error with $form->isValid()
        {
            $em = $this->getDoctrine()->getManager();

            $em->persist($note);
            $em->flush();

            return $this->json($note, 201, [], ['groups' => 'readNote']);

        } else{
            return $this->json('erreur', 201, []);

        }

    }

}
