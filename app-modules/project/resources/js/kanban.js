/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
document.addEventListener('alpine:init', () => {
    Alpine.data('kanban', ($wire) => ({
        init() {
            const kanbanLists = document.querySelectorAll('[id^="kanban-list-"]');

            kanbanLists.forEach((kanbanList) => {
                window.Sortable.create(kanbanList, {
                    group: 'kanban',
                    animation: 100,
                    forceFallback: true,
                    dragClass: 'drag-card',
                    ghostClass: 'ghost-card',
                    easing: 'cubic-bezier(0, 0.55, 0.45, 1)',
                    onAdd: async function (evt) {
                        try {
                            const result = await $wire.movedTask(
                                evt.item.dataset.task,
                                evt.from.dataset.status,
                                evt.to.dataset.status,
                            );

                            if (result.original.success) {
                                new FilamentNotification()
                                    .icon('heroicon-o-check-circle')
                                    .title(result.original.message)
                                    .iconColor('success')
                                    .send();
                            } else {
                                new FilamentNotification()
                                    .icon('heroicon-o-x-circle')
                                    .title(result.original.message)
                                    .iconColor('danger')
                                    .send();
                            }
                        } catch (e) {
                            new FilamentNotification()
                                .icon('heroicon-o-x-circle')
                                .title('Something went wrong, please try again later')
                                .iconColor('danger')
                                .send();
                        }
                    },
                });
            });
        },
    }));
});
