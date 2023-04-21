<?php

namespace App\Admin\Controller;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/admin/galleria", name="admin_gallery_")
 */
class GalleryController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id, ParameterBagInterface $param)
    {
        $entity = $this->entityManager->getRepository(Gallery::class)->findOneBy(['id' => $id]);
        if (!$entity instanceof Gallery) {
            throw $this->createNotFoundException('A kép nem található.');
        }
        
        try {
            $filesystem = new Filesystem();
            $filesystem->remove($param->get('file_upload_dir') . $entity->getPath());
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            $this->addFlash('notice', 'Sikeres törlés.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen törlés.');
        }

        return $this->redirectToRoute('admin_service_form', ['id' => $entity->getService()->getId(),
            'op' => strtolower($entity->getService()->getServiceType())]);
    }
}
