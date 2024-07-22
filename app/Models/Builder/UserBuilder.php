<?php

namespace App\Models\Builder;

use App\Traits\AuthUserTrait;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    use AuthUserTrait;

    /**
     * @param string|null $keyword
     *
     * @return UserBuilder|$this
     */
    public function whereSearch(string|null $keyword): UserBuilder
    {
        if ($keyword) {
            $keyword = addcslashes($keyword, '%_\\[]^$*()');
            return $this->where('full_name', 'like', '%'.$keyword.'%')
                ->orWhere(function ($query) use ($keyword) {
                    $names = explode(' ', $keyword);
                    for ($i = 0; $i < count($names); $i++) {
                        $query
                            ->orWhere('first_name', 'like', '%'.$names[$i].'%')
                            ->orWhere('last_name', 'like', '%'.$names[$i].'%');
                    }
                })
                ->orWhere('first_name', 'like', '%'.$keyword.'%')
                ->orWhere('last_name', 'like', '%'.$keyword.'%')
                ->orWhere('username', 'like', '%'.$keyword.'%')
                ->orWhere('email', 'like', '%'.$keyword.'%');
        }

        return $this;
    }
}
