<?php

namespace App\Filament\Resources\CompanyProfiles\Pages;

use App\Filament\Resources\CompanyProfiles\CompanyProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyProfile extends CreateRecord
{
    protected static string $resource = CompanyProfileResource::class;

    public function getTitle(): string
    {
        return 'Nueva empresa';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}