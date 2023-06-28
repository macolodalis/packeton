<?php

namespace Packeton\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Packeton\Attribute\Vars;
use Packeton\Entity\SubRepository;
use Packeton\Form\Type\SubRepositoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/subrepository')]
class SubRepositoryController extends AbstractController
{
    public function __construct(
        protected ManagerRegistry $registry
    ) {
    }

    #[Route('/create', name: 'subrepository_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function createAction(Request $request): Response
    {
        $entity = new SubRepository();
        return $this->handleUpdate($request, $entity, 'Saved successfully');
    }

    #[Route('/{id}/update', name: 'subrepository_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function updateAction(Request $request, #[Vars] SubRepository $entity): Response
    {
        return $this->handleUpdate($request, $entity, 'Saved successfully');
    }

    #[Route('/{id}/switch', name: 'subrepository_switch')]
    public function switchAction(Request $request, #[Vars] SubRepository $entity): Response
    {
        $request->getSession()->set('_sub_repo', $entity->getId());
        return new RedirectResponse('/');
    }

    #[Route('/switch-root', name: 'subrepository_switch_root')]
    public function switchRoot(Request $request): Response
    {
        $request->getSession()->remove('_sub_repo');
        return new RedirectResponse('/');
    }

    protected function handleUpdate(Request $request, SubRepository $group, string $flashMessage)
    {
        $form = $this->createForm(SubRepositoryType::class, $group);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $group = $form->getData();
                $em = $this->registry->getManager();
                $em->persist($group);
                $em->flush();

                $this->addFlash('success', $flashMessage);
                return new RedirectResponse($this->generateUrl('groups_index'));
            }
        }

        $data = [
            'form' => $form->createView(),
            'entity' => $group
        ];

        return $this->render('subrepository/update.html.twig', $data);
    }
}
