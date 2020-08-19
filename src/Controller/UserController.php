<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAdminType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("modifierProfil", name="user_modifierProfil")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRepository $repository
     * @param $entityManager
     * @return Response
     */
    public function modifierProfil(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, UserRepository $repository)
    {
        $user=$this->getUser();
        //récupération du pseudo du user
        $oldUsername = $user->getUsername();

        //création instance du formulaire
        $userForm = $this->createForm(UserType::class, $user);

        //association du formulaire avec la request
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $usernameIsUnique = $repository->findOneBy(["username" => $user->getUsername()]);

            if ($usernameIsUnique === null || $user->getUsername() === $oldUsername) {
                $hashed = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hashed);



                $this->addFlash('success', 'Le profil a bien été mis à jour');


                //Récupération de la photo et valorisation de la variable $photoFile
                $photoFile = $userForm->get('photo')->getData();


                // La photo doit etre traitée que lorsque le fichier est téléchargé
                if ($photoFile) {

                    $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                    // Déplacer le fichier vers l'emplacement de stockage
                    try {
                        $photoFile->move(
                            $this->getParameter('photo_directory'),
                            $newFilename
                        );
                        $user->setPhoto($newFilename);

                    } catch (FileException $e) {
                        // Levée d'exception si il y a un probleme de téléchargement
                        //FIXME faire exception
                    }

                }

                $manager->persist($user);
                $manager->flush();

                return $this->render("user/detailProfil.html.twig", ['user' => $user]);

            } else {
                $this->addFlash('error', 'Désolé, le pseudo est déja utilisé');
            }


    }

        return $this->render("user/modifierProfil.html.twig", ['userForm' => $userForm->createView()]);
    }

        /**
         * @Route("detailProfil/{id}", name="user_detailProfil", requirements={"id":"\d+"})
         * @ParamConverter()
         */
        public
        function detailProfil(User $user)
        {


            return $this->render("user/detailProfil.html.twig", ['user' => $user]);
        }
}
