<?php

namespace App\Http\Controllers\Api\Manage\Report;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\Report\Resource\ResourceReportRepository;
use App\Repositories\Api\Manage\ResourceCollection\ResourceCollectionRepository;
use App\Repositories\Api\Manage\ResourceGroup\ResourceGroupRepository;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use Symfony\Component\HttpFoundation\Response;

class ResourceReportController extends AppBaseController
{
    public function __construct(
        protected ResourceReportRepository $resourceReportRepository,
        protected ResourceModuleRepository $resourceModuleRepository,
        protected ResourceGroupRepository $resourceGroupRepository,
        protected ResourceCollectionRepository $resourceCollectionRepository
    ) {
    }

    public function getResourceModuleMemberProgress(string $slug)
    {
        try {
            $resourceModule = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);

            if ($resourceModule) {
                $memberProgress = $this->resourceReportRepository->getResourceModuleMemberProgress($resourceModule);

                if ($memberProgress !== false) {
                    return $this->sendResponse($memberProgress, __('Resource module member progress'));
                }

                return $this->sendError(__('responses.failed_to_fetch_resource_member_progress'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_resource_module_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_fetch_resource_member_progress'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getResourceGroupMemberProgress(string $slug)
    {
        try {
            $resourceGroup = $this->resourceGroupRepository->getResourceGroupBasedOnSlug($slug);

            if ($resourceGroup) {
                $memberProgress = $this->resourceReportRepository->getResourceGroupMemberProgress($resourceGroup);

                if ($memberProgress !== false) {
                    return $this->sendResponse($memberProgress, __('Resource group member progress'));
                }

                return $this->sendError(__('responses.failed_to_fetch_resource_group_member_progress'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_resource_group_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_fetch_resource_group_member_progress'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getResourceCollectionMemberProgress(string $slug)
    {
        try {
            $resourceCollection = $this->resourceCollectionRepository->getResourceCollectionBasedOnSlug($slug);

            if ($resourceCollection) {
                $memberProgress = $this->resourceReportRepository->getResourceCollectionMemberProgress($resourceCollection);

                if ($memberProgress !== false) {
                    return $this->sendResponse($memberProgress, __('Resource collection member progress'));
                }

                return $this->sendError(__('responses.failed_to_fetch_resource_collection_member_progress'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_resource_collection_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_fetch_resource_collection_member_progress'), Response::HTTP_BAD_REQUEST);
        }
    }
}
