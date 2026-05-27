<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Resources\Pages\TreePage;

class MenuTree extends TreePage
{
    protected static string $resource = MenuResource::class;

    protected static int $maxDepth = 4;

    protected function hasDeleteAction(): bool
    {
        return true;
    }

    protected function hasEditAction(): bool
    {
        return true;
    }

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function configureCreateAction(CreateAction $action): CreateAction
    {
        return parent::configureCreateAction($action)->slideOver();
    }

    protected function configureEditAction(EditAction $action): EditAction
    {
        return parent::configureEditAction($action)->slideOver();
    }

    protected function configureDeleteAction(DeleteAction $action): DeleteAction
    {
        return parent::configureDeleteAction($action)->requiresConfirmation();
    }

    public function getTreeRecordTitle(?Model $record = null): string
    {
        if (! $record) {
            return '';
        }

        $typeBadge = match ($record->type) {
            'route' => '🚌 Tuyến đường',
            'page' => '📄 Trang tĩnh',
            'system_page' => '⚙️ Trang hệ thống',
            default => '🔗 Liên kết',
        };

        $target = $record->type === 'route' ? "ID: {$record->related_id}" : $record->url;

        // Dùng text thuần túy thay vì HTML để tránh lỗi vỡ JSON x-data của AlpineJS
        return "{$record->name} — [{$typeBadge} | {$target}]";
    }
}
