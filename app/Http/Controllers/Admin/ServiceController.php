<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Common\ActionNotificationMail;
use App\Services\Service\ServiceService;
use Illuminate\Support\Facades\Mail;

class ServiceController extends Controller
{
    public function index(ServiceService $serviceService)
    {
        $services = $serviceService->getAllServicesForAdmin();

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
                'total' => $services->count(),
            ],
            'message' => 'Services retrieved successfully'
        ], 200);
    }

    public function publishedCount(ServiceService $serviceService)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'published_services' => $serviceService->getPublishedServicesCount(),
            ],
            'message' => 'Number of published services'
        ], 200);
    }

    public function destroy(int $id, ServiceService $serviceService)
    {
        $service = $serviceService->getServiceById($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Service not found'
            ], 404);
        }

        $service->loadMissing('professional.user', 'category');
        $serviceService->deleteService($service);

        $professionalUser = $service->professional?->user;

        if ($professionalUser?->email) {
            Mail::to($professionalUser->email)->send(new ActionNotificationMail(
                'Votre service a ete retire',
                'Service retire',
                'Bonjour ' . $professionalUser->name . ',',
                'Un administrateur a retire un de vos services de la plateforme.',
                [
                    'Service' => $service->title,
                    'Categorie' => $service->category?->name ?? 'N/A',
                    'Ville' => $service->city ?? 'N/A',
                ],
                'Voir mes services',
                url('/')
            ));
        }

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Service deleted successfully'
        ], 200);
    }
}
