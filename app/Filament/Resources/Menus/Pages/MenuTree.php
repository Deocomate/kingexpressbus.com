<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use App\Support\PriorityOrder;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
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

    /**
     * Filament Tree assigns ascending order numbers (top = 1).
     * Invert per sibling group so higher priority displays first on client.
     */
    public function updateTree(?array $list = null): array
    {
        if ($list) {
            $records = $this->getRecords()->keyBy(fn ($record) => $record->getAttributeValue($record->getKeyName()));

            $unnestedArr = [];
            $defaultParentId = $this->getTreeRootLevelKey();

            $this->unnestMenuTreeArray($unnestedArr, $list, $defaultParentId);
            $unnestedArr = PriorityOrder::invertSiblingOrders($unnestedArr);

            $needReload = false;

            $unnestedArrData = collect($unnestedArr)
                ->map(fn (array $data, $id) => ['data' => $data, 'model' => $records->get($id)])
                ->filter(fn (array $arr) => ! is_null($arr['model']));

            foreach ($unnestedArrData as $arr) {
                $model = $arr['model'];
                [$newParentId, $newOrder] = [$arr['data']['parent_id'], $arr['data']['order']];

                if ($model instanceof Model) {
                    $parentColumnName = $model->determineParentColumnName();
                    $orderColumnName = $model->determineOrderColumnName();
                    $newParentId = $newParentId === $defaultParentId ? $model::defaultParentKey() : $newParentId;

                    $model->{$parentColumnName} = $newParentId;
                    $model->{$orderColumnName} = $newOrder;

                    if ($model->isDirty([$parentColumnName, $orderColumnName])) {
                        $model->save();
                        $needReload = true;
                    }
                }
            }

            if ($needReload) {
                Notification::make()
                    ->success()
                    ->title(__('filament-actions::edit.single.notifications.saved.title'))
                    ->send();

                $this->dispatch('refreshTree');
            }

            return ['reload' => $needReload];
        }

        return ['reload' => false];
    }

    private function unnestMenuTreeArray(array &$result, array $current, $parent): void
    {
        foreach ($current as $index => $item) {
            $key = data_get($item, 'id');
            $result[$key] = [
                'parent_id' => $parent,
                'order' => $index + 1,
            ];

            if (isset($item['children']) && count($item['children'])) {
                $this->unnestMenuTreeArray($result, $item['children'], $key);
            }
        }
    }
}
