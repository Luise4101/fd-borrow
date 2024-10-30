<?php
    use Filament\Forms\Components\Actions\Action;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\MaxWidth;

    $containers = $getChildComponentContainers();

    $addAction = $getAction($getAddActionName());
    $cloneAction = $getAction($getCloneActionName());
    $deleteAction = $getAction($getDeleteActionName());
    $moveDownAction = $getAction($getMoveDownActionName());
    $moveUpAction = $getAction($getMoveUpActionName());
    $reorderAction = $getAction($getReorderActionName());
    $isReorderableWithButtons = $isReorderableWithButtons();
    $extraItemActions = $getExtraItemActions();
    $extraActions = $getExtraActions();
    $visibleExtraItemActions = [];
    $visibleExtraActions = [];

    $headers = $getHeaders();
    $renderHeader = $shouldRenderHeader();
    $stackAt = $getStackAt();
    $hasContainers = count($containers) > 0;
    $emptyLabel = $getEmptyLabel();
    $streamlined = $isStreamlined();

    $statePath = $getStatePath();

    foreach ($extraActions as $extraAction) {
        $visibleExtraActions = array_filter(
            $extraActions,
            fn (Action $action): bool => $action->isVisible(),
        );
    }

    foreach ($extraItemActions as $extraItemAction) {
        $visibleExtraItemActions = array_filter(
            $extraItemActions,
            fn (Action $action): bool => $action->isVisible(),
        );
    }

    $hasActions = $reorderAction->isVisible()
        || $cloneAction->isVisible()
        || $deleteAction->isVisible()
        || $moveUpAction->isVisible()
        || $moveDownAction->isVisible()
        || filled($visibleExtraItemActions);
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $getFieldWrapperView()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field]); ?>
    <div
        x-data="{}"
        <?php echo e($attributes->merge($getExtraAttributes())->class([
            'table-repeater-component space-y-6 relative',
            'streamlined' => $streamlined,
            match ($stackAt) {
                'sm', MaxWidth::Small => 'break-point-sm',
                'lg', MaxWidth::Large => 'break-point-lg',
                'xl', MaxWidth::ExtraLarge => 'break-point-xl',
                '2xl', MaxWidth::TwoExtraLarge => 'break-point-2xl',
                default => 'break-point-md',
            }
        ])); ?>

    >
        <!--[if BLOCK]><![endif]--><?php if(count($containers) || $emptyLabel !== false): ?>
            <div class="table-repeater-container rounded-xl relative ring-1 ring-gray-950/5 dark:ring-white/20">
                <table class="w-full">
                    <thead class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'table-repeater-header-hidden sr-only' => ! $renderHeader,
                        'table-repeater-header rounded-t-xl overflow-hidden border-b border-gray-950/5 dark:border-white/20' => $renderHeader,
                    ]); ?>">
                    <tr class="text-xs md:divide-x md:divide-gray-950/5 dark:md:divide-white/20">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th
                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'table-repeater-header-column p-2 font-medium first:rounded-tl-xl last:rounded-tr-xl bg-gray-100 dark:text-gray-300 dark:bg-gray-900',
                                    match($header->getAlignment()) {
                                      'center', Alignment::Center => 'text-center',
                                      'right', 'end', Alignment::Right, Alignment::End => 'text-end',
                                      default => 'text-start'
                                    }
                                ]); ?>"
                                style="width: <?php echo e($header->getWidth()); ?>"
                            >
                                <?php echo e($header->getLabel()); ?>

                                <!--[if BLOCK]><![endif]--><?php if($header->isRequired()): ?>
                                    <span class="whitespace-nowrap">
                                        <sup class="font-medium text-danger-700 dark:text-danger-400">*</sup>
                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($hasActions && count($containers)): ?>
                            <th class="table-repeater-header-column w-px last:rounded-tr-xl p-2 bg-gray-100 dark:bg-gray-900">
                                <span class="sr-only">
                                    <?php echo e(trans('table-repeater::components.repeater.row_actions.label')); ?>

                                </span>
                            </th>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tr>
                    </thead>
                    <tbody
                        x-sortable
                        wire:end.stop="<?php echo e('mountFormComponentAction(\'' . $statePath . '\', \'reorder\', { items: $event.target.sortable.toArray() })'); ?>"
                        class="table-repeater-rows-wrapper divide-y divide-gray-950/5 dark:divide-white/20"
                    >
                    <?php if(count($containers)): ?>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $containers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uuid => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $visibleExtraItemActions = array_filter(
                                    $extraItemActions,
                                    fn (Action $action): bool => $action(['item' => $uuid])->isVisible(),
                                );
                            ?>
                            <tr
                                wire:key="<?php echo e($this->getId()); ?>.<?php echo e($row->getStatePath()); ?>.<?php echo e($field::class); ?>.item"
                                x-sortable-item="<?php echo e($uuid); ?>"
                                class="table-repeater-row"
                            >
                                <?php ($counter = 0); ?>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $row->getComponents(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <!--[if BLOCK]><![endif]--><?php if($cell instanceof \Filament\Forms\Components\Hidden || $cell->isHidden()): ?>
                                        <?php echo e($cell); ?>

                                    <?php else: ?>
                                        <td
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'table-repeater-column',
                                                'p-2' => ! $streamlined,
                                                'has-hidden-label' => $cell->isLabelHidden(),
                                                match($headers[$counter++]->getAlignment()) {
                                                  'center', Alignment::Center => 'text-center',
                                                  'right', 'end', Alignment::Right, Alignment::End => 'text-end',
                                                  default => 'text-start'
                                                }
                                            ]); ?>"
                                            style="width: <?php echo e($cell->getMaxWidth() ?? 'auto'); ?>"
                                        >
                                            <?php echo e($cell); ?>

                                        </td>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                <!--[if BLOCK]><![endif]--><?php if($hasActions): ?>
                                    <td class="table-repeater-column p-2 w-px">
                                        <ul class="flex items-center table-repeater-row-actions gap-x-3 px-2">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $visibleExtraItemActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extraItemAction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <?php echo e($extraItemAction(['item' => $uuid])); ?>

                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($reorderAction->isVisible()): ?>
                                                <li x-sortable-handle class="shrink-0">
                                                    <?php echo e($reorderAction); ?>

                                                </li>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($isReorderableWithButtons): ?>
                                                <!--[if BLOCK]><![endif]--><?php if(! $loop->first): ?>
                                                    <li>
                                                        <?php echo e($moveUpAction(['item' => $uuid])); ?>

                                                    </li>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <!--[if BLOCK]><![endif]--><?php if(! $loop->last): ?>
                                                    <li>
                                                        <?php echo e($moveDownAction(['item' => $uuid])); ?>

                                                    </li>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($cloneAction->isVisible()): ?>
                                                <li>
                                                    <?php echo e($cloneAction(['item' => $uuid])); ?>

                                                </li>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                            <!--[if BLOCK]><![endif]--><?php if($deleteAction->isVisible()): ?>
                                                <li>
                                                    <?php echo e($deleteAction(['item' => $uuid])); ?>

                                                </li>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </ul>
                                    </td>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    <?php else: ?>
                        <tr class="table-repeater-row table-repeater-empty-row">
                            <td colspan="<?php echo e(count($headers) + intval($hasActions)); ?>"
                                class="table-repeater-column table-repeater-empty-column p-4 w-px text-center italic">
                                <?php echo e($emptyLabel ?: trans('table-repeater::components.repeater.empty.label')); ?>

                            </td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($addAction->isVisible() || filled($visibleExtraActions)): ?>
            <ul class="relative flex gap-4 justify-center">
                <?php if($addAction->isVisible()): ?>
                    <li>
                        <?php echo e($addAction); ?>

                    </li>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if(filled($visibleExtraActions)): ?>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $visibleExtraActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extraAction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <?php echo e(($extraAction)); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </ul>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH D:\pmwatis\fd-borrow\vendor\awcodes\filament-table-repeater\src\/../resources/views/components/table-repeater.blade.php ENDPATH**/ ?>