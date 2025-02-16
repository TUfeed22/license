<?php

namespace App\Controller;

use App\Form\FileUploadType;
use App\Service\LicenseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LicenseController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $form = $this->createForm(FileUploadType::class);

        return $this->render('license/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/get-licenses', name: 'get_licenses')]
    public function getLicenses(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file_upload')['file'];

        if (empty($file)) {
            return new JsonResponse(['error' => 'Файл не загружен или имеет неверный тип.'], 400);
        }

        try {
            $licenses = new LicenseService()->getLicenses($file);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }

        return new JsonResponse(['licenses' => $licenses]);
    }

}
