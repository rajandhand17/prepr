<?php

namespace App\Models\Builder;

use App\Traits\AuthUserTrait;
use Illuminate\Database\Eloquent\Builder;

class OrganizationBuilder extends Builder
{
    use AuthUserTrait;

    /**
     * @param string|null $keyword
     *
     * @return OrganizationBuilder|$this
     */
    public function whereSearch(string|null $keyword): OrganizationBuilder
    {
        if ($keyword) {
            $keyword = addcslashes($keyword, '%_\\[]^$*()');

            return $this->where('language', '=', app()->getLocale())
                ->where('title', 'like', '%'.$keyword.'%')
                ->orWhere('display_name', 'like', '%'.$keyword.'%');
        }

        return $this;
    }

    /**
     * @return $this|OrganizationBuilder
     */
    public function whereVerified(): OrganizationBuilder
    {
        $allowedGlobal = $this->allowedGlobalSearch();
        if (!$allowedGlobal) {
            $organizationIds = $this->getUserOrganizationIds();

            return $this->where(function ($query) use ($organizationIds) {
                $query->where('user_id', '=', auth()->id())->orWhere('is_verified', '=', '1')->orWhereIn('id', $organizationIds);
            });
        }

        return $this;
    }
}
