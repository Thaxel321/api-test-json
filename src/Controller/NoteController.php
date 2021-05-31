<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Entity\Notes;
use App\Repository\NotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class NoteController extends AbstractController
{
    /**
     * @Route ("/api/note/new/{id}", name="note", methods={"POST"})
     * @OA\Post (
     *     tags={"Note"},
     *      @OA\Parameter (
     *              name="id",
     *              in="path",
     *              @OA\Schema (type="integer"),
     *          ),
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur")
     *          )
     *      )
     * )
     *
     */
    public function showNote(int $id ,Request $request, EntityManagerInterface $em){

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $eleve = $em->getRepository(Eleves::class)->find($id);

        try {

            if ($data['valeur'] >= 0 && $data['valeur'] <= 20) {
                return $this->json('La note doit être comprise entre 0 et 20', 400);
            }elseif ($data['matiere'] == ''){
                return $this->json('Le champ matiere ne doit pas être vide', 400);
            }

            $note = new Notes();
            $note->setMatiere($data['matiere'])
                 ->setValeur($data['valeur'])
                 ->setEleve($eleve);

            $em->persist($note);
            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/note/edit/{id}", name="editNote", methods={"PUT"})
     * @OA\Put(
     *     tags={"Note"},
     *     @OA\Parameter(
     *         name="id",
     *         in = "path",
     *         @OA\Schema(type="integer"),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"valeur, matiere"},
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur")
     *          )
     *      )
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
     * @Route("/api/note/delete/{id}", name="deleteNote", methods={"DELETE"})
     * @OA\Delete(
     *     tags={"Note"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         @OA\Schema(type="integer")
     *      )
     * )
     */

    public function supprimerEleve(int $id, EntityManagerInterface $em){

            try{
                $note = $em->getRepository(Notes::class)->find($id);

                if ($note == null){
                    return $this->json("Aucune notes ne possède l'id (" .$id.")" , 400);
                }

                $em->remove($note);
                $em->flush();

                return $this->json("La note possèdant l' ".$id." a été supprimé",
                    200, [], []);
            } catch (NotEncodableValueException $e){
                return $this->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }

        }
}
